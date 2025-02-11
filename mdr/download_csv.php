<?php
// Include the authentication check
include 'auth_check.php';

// Ensure only admins can access this page
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php'); // Redirect non-admin users to home.php
    exit();
}
// Include the database connection file
include('db_connection.php');

// Get the teacher_id and month from the form submission
$teacher_id = $_POST['teacher_id'] ?? null;
$month = $_POST['month'] ?? null;

if ($teacher_id && $month) {
    // Extract year and month from the selected date
    $year = date("Y", strtotime($month));
    $month_number = date("m", strtotime($month));

    // Query to get hourly rate, distance_from_home, and per_kilometer_rate from Teacher table
    $stmt = $conn->prepare("
        SELECT hourly_rate, kyori, per_kilometer_rate, name 
        FROM teacher 
        WHERE teacher_id = ?
    ");
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teacher_data = $result->fetch_assoc();
        $hourly_rate = $teacher_data['hourly_rate'];
        $distance = $teacher_data['kyori'];
        $per_kilometer_rate = $teacher_data['per_kilometer_rate'];
        $teacher_name = $teacher_data['name'];

        // Query to sum the total working hours (comasuu) from Lecture table
        $stmt = $conn->prepare("
            SELECT SUM(comasuu) AS total_hours 
            FROM kosiki 
            WHERE teacher_id = ? 
            AND YEAR(lecture_date) = ? 
            AND MONTH(lecture_date) = ?
        ");
        $stmt->bind_param("sss", $teacher_id, $year, $month_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $lecture_data = $result->fetch_assoc();
        $total_hours = $lecture_data['total_hours'] ?? 0;

        // Calculate transportation fee
        $transportation_fee = $distance * $per_kilometer_rate;

        // Calculate total salary (including transportation fee)
        $total_salary = ($total_hours * $hourly_rate) + $transportation_fee;

        // Deduct 10% tax from the salary (講師謝金)
        $tax_deduction = ($total_hours * $hourly_rate) * 0.10; // 10% tax on salary
        $salary_after_tax = ($total_hours * $hourly_rate) - $tax_deduction; // Salary after tax deduction

        // Grand total after tax deduction (including transportation fee)
        $grand_total_after_tax = $salary_after_tax + $transportation_fee;

        // Generate the invoice with separate sheets for salary and transportation fee
        echo "
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>給与明細書</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/jspdf-autotable'></script>
    <script src='notoSerifFont.js'></script>
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            
        }
        .card-header {
            background-color: rgb(57, 167, 152);
            color: white;
            text-align: center;
        }
        .sheet {
            border-radius: 10px;
            margin: 15px 0;
            padding: 15px;
            background-color: #fff;
        }
        .sheet h5 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
        }
        th {
            background-color: #f1f1f1;
        }
        .btn {
            margin-top: 10px;
            margin-bottom: 40px;
            background-color: rgb(57, 167, 152)!important;
            border-color: rgb(57, 167, 152) !important;
        }
    </style>
</head>
<body class='bg-light'>
    <div class='container'>
        <div class='card'>
            <div class='card-header'>
                <h2>給与明細書</h2>
            </div>
            <div class='card-body'>
                <p><strong>発行日:</strong> " . date("Y年m月d日") . "</p>
                <p><strong>請求番号:</strong> INV-" . uniqid() . "</p>
                <p><strong>教員名:</strong> $teacher_name 様</p>
                <p><strong>ご請求書:</strong> ¥" . number_format($grand_total_after_tax) . "</p>
                
                <!-- Salary Sheet -->
                <div class='sheet'>
                    <table>
                        <tr>
                            <th>項目</th>
                            <th>詳細</th>
                        </tr>
                        <tr>
                            <td>教員ID</td>
                            <td>$teacher_id</td>
                        </tr>
                        <tr>
                            <td>年月</td>
                            <td>$month</td>
                        </tr>
                        <tr>
                            <td>コマ数合計</td>
                            <td>$total_hours コマ</td>
                        </tr>
                        <tr>
                            <td>単価</td>
                            <td>¥" . number_format($hourly_rate) . "</td>
                        </tr>
                        <tr>
                            <td>講師謝金</td>
                            <td>¥" . number_format($total_hours * $hourly_rate) . "</td>
                        </tr>
                        <tr>
                            <td>所得税 (10%)</td>
                            <td>¥" . number_format($tax_deduction) . "</td>
                        </tr>
                        <tr>
                            <td>税引後講師謝金</td>
                            <td>¥" . number_format($salary_after_tax) . "</td>
                        </tr>
                    </table>
                </div>

                <!-- Transportation Fee Sheet -->
                <div class='sheet'>
                    <h5>交通費</h5>
                    <table>
                        <tr>
                            <th>項目</th>
                            <th>詳細</th>
                        </tr>
                        <tr>
                            <td>距離</td>
                            <td>$distance km</td>
                        </tr>
                        <tr>
                            <td>1kmあたりの料金</td>
                            <td>¥" . number_format($per_kilometer_rate) . "</td>
                        </tr>
                        <tr>
                            <td>交通費</td>
                            <td>¥" . number_format($transportation_fee) . "</td>
                        </tr>
                    </table>
                </div>
                <p><strong>支払期日:</strong> " . date("Y年m月d日", strtotime("+30 days")) . "</p>
                <p><strong>振込先:</strong> 三井住友銀行 渋谷支店 普通口座 12345678</p>
            </div>
            <div class='card-footer text-center'>
                <button class='btn btn-primary w-100' id='download-btn'>ダウンロード</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('download-btn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add the Noto Serif font
            doc.addFileToVFS('NotoSerifJP-VariableFont_wght.ttf', notoSerifBase64);
            doc.addFont('NotoSerifJP-VariableFont_wght.ttf', 'NotoSerifJP', 'normal');
            doc.setFont('NotoSerifJP', 'normal');
            doc.setFontSize(16);

            // Title and details
            doc.text('発行日: " . date("Y年m月d日") . "', 10, 20);
            doc.text('請求番号: INV-" . uniqid() . "', 10, 30);
            
            // Salary Sheet Table
            doc.autoTable({
                head: [['項目', '詳細']],
                body: [
                    ['教員ID', '$teacher_id'],
                    ['年月', '$month'],
                    ['合計コマ数', '$total_hours コマ'],
                    ['単価', '¥" . number_format($hourly_rate) . "'],
                    ['講師謝金', '¥" . number_format($total_hours * $hourly_rate) . "'],
                    ['所得税 (10%)', '¥" . number_format($tax_deduction) . "'],
                    ['税引後講師謝金', '¥" . number_format($salary_after_tax) . "']
                ],
                startY: 50,
                styles: {
                    font: 'NotoSerifJP',
                    fontSize: 12
                }
            });

            // Transportation Fee Sheet Table
            doc.text('交通費', 10, doc.lastAutoTable.finalY + 20);
            doc.autoTable({
                head: [['項目', '詳細']],
                body: [
                    ['距離', '$distance km'],
                    ['1kmあたりの料金', '¥" . number_format($per_kilometer_rate) . "'],
                    ['交通費', '¥" . number_format($transportation_fee) . "']
                ],
                startY: doc.lastAutoTable.finalY + 30,
                styles: {
                    font: 'NotoSerifJP',
                    fontSize: 12
                }
            });

            doc.text('ご請求書: ¥" . number_format($grand_total_after_tax) . "', 10, doc.lastAutoTable.finalY + 40);
            doc.save('salary_invoice.pdf');
        });
    </script>
</body>
</html>
        ";
    }
}
?>
