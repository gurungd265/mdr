// main.js
doc.addFileToVFS("NotoSansJP-Regular.ttf", notoSansBase64);
doc.addFont("NotoSansJP-Regular.ttf", "NotoSansJP", "normal");
doc.setFont("NotoSansJP");

// Add content
doc.text("こんにちは、世界!", 10, 10);

// Save the PDF
doc.save("customFont.pdf");
