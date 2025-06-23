# OTP-Based Online Voting System 

A secure, web-based online voting platform that uses OTP authentication to ensure only registered voters can cast their votes. This system is designed to provide transparency, ease of access, and reduce the limitations of traditional voting methods.

##🔧 Features

- ✅ Voter Registration and Verification  
- 🔐 OTP-based secure login  
- 🗳 Vote casting for listed candidates  
- 🧑‍💼 Admin panel to view and manage votes  
- 📊 Vote result visualization  
- 📄 Real-time alerts and confirmation messages

## 👨‍💻 Technologies Used

- *Frontend:* HTML, CSS  
- *Backend:* PHP  
- *Database:* MySQL  
- *Local Server:* XAMPP
- 
## 🏁 How It Works

1. Voter visits home.php and registers using their name and mobile number.
2. The system verifies their name in the database.
3. If matched, an OTP is generated (or in production, sent via SMS).
4. After OTP login, the voter casts their vote.
5. Voter receives a confirmation message.
6. Admin can log in to view and analyze results.

## 📦 Requirements

- PHP 7.x or above
- MySQL
- XAMPP or any local server
- Modern Web Browser

## 📚 Future Scope

- 🔒 Integration with Aadhaar or biometric authentication  
- 🌐 Multilingual support for accessibility  
- 📲 Mobile app version  
- 📊 Live vote analytics
