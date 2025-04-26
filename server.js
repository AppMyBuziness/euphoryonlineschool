const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = 3000;
const messagesFilePath = path.join(__dirname, 'messages.json');

app.use(bodyParser.json());
app.use(express.static('public')); // Serve static files from "public" directory

// Load messages from JSON file
app.get('/messages', (req, res) => {
    fs.readFile(messagesFilePath, 'utf8', (err, data) => {
        if (err) return res.status(500).send(err);
        res.send(JSON.parse(data || '[]')); // Send messages as an array
    });
});

// Save a new message to the JSON file
app.post('/messages', (req, res) => {
    const newMessage = req.body;
    
    fs.readFile(messagesFilePath, 'utf8', (err, data) => {
        if (err) return res.status(500).send(err);
        const messages = JSON.parse(data || '[]');
        messages.push(newMessage);
        
        fs.writeFile(messagesFilePath, JSON.stringify(messages, null, 2), err => {
            if (err) return res.status(500).send(err);
            res.status(201).send('Message saved');
        });
    });
});

// Start the server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});