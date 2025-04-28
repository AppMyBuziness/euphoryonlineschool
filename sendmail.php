<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $to = 'appmybuziness@gmail.com'; // Replace with your email address
    $senderEmail = filter_var(trim($_POST['senderEmail']), FILTER_SANITIZE_EMAIL);
    $subject = 'New Assignment Submission';
    $message = 'A new assignment has been submitted.';
    $headers = "From: $senderEmail"; // Use the user's email as the sender

    // Handle file upload
    $file = $_FILES['pdfFile'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileType = $file['type'];

        // Check if the file is a PDF
        if ($fileType === 'application/pdf') {
            $fileContent = chunk_split(base64_encode(file_get_contents($fileTmpPath)));
            $separator = md5(time());
            $eol = "\r\n";

            // Build the email headers
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

            // Build the email body
            $body = "--" . $separator . $eol;
            $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
            $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
            $body .= $message . $eol;
            $body .= "--" . $separator . $eol;
            $body .= "Content-Type: application/pdf; name=\"" . $fileName . "\"" . $eol;
            $body .= "Content-Transfer-Encoding: base64" . $eol;
            $body .= "Content-Disposition: attachment; filename=\"" . $fileName . "\"" . $eol . $eol;
            $body .= $fileContent . $eol;
            $body .= "--" . $separator . "--";

            // Send the email
            if (mail($to, $subject, $body, $headers)) {
                header("Location: assignments.html?message=" . urlencode("Email sent successfully!"));
            } else {
                header("Location: assignments.html?message=" . urlencode("Email sending failed. Check your mail server settings."));
            }
        } else {
            header("Location: assignments.html?message=" . urlencode("Uploaded file is not a PDF. Please upload a PDF file."));
        }
    } else {
        header("Location: assignments.html?message=" . urlencode("Error uploading file. Error code: " . $file['error']));
    }
} else {
    header("Location: assignments.html?message=" . urlencode("Invalid request method."));
}
?>