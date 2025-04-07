# 🛒 E-Commerce - PHP Online Store Website

A complete e-commerce website built with PHP, MySQL, OOP, Bootstrap, and REST API. It includes both frontend and backend functionality, a shopping cart, user registration/login, an admin panel, PDF invoice generation, and more.

## 🚀 Features Overview

- ✅ Browse and search electronic products
- ✅ User registration and login
- ✅ Admin can manage users and products (CRUD)
- ✅ Product image upload
- ✅ Shopping cart: add, update, remove items
- ✅ PDF invoice generation at checkout
- ✅ REST API (JSON)
- ✅ Secure handling (PDO, prepared statements, prevent SQL/XSS injection)
- ✅ AODA compliant (image descriptions, Bootstrap accessibility support)

## 🗂 Project Structure

creating an e-commerce website to sell electronic products. 

You will need to create an administration panel for admin users only which allows an admin to create, read, update and delete both products and users. 

The project should have a basic e-Commerce website. A user should be able to navigate through a number of products. The user should be able to use filters on products. The user should be able to add products to a cart in different quantities and modify the quantities or remove the products. Before checkout, the user should be able to register. At checkout, the user should get a pdf invoice. (implementing a payment gateway is the future scope). 

There should be a login screen which allows users to login based on a previous registration.

Create a SQL database according to the requirements of the website. The database should be normalized to at least the 3rd Normal Form.

Your website should implement the following features:

1. You will need to organize your code into classes. Using Object-Oriented Programming accounts for a large portion of the total mark.

2. CRUD operation using Object-Oriented Programming in PHP.  This means an administrator can create, read, update and delete both products and users from the database in an admin panel. You should use an object oriented database API like PDO or the object oriented version of the MySQLi API (uses the -> for object method access)
3. At least 5 products in database
4. The admin panel should also feature a photo upload feature for uploading product photos to the server. 
5. Forms should be self-processing, sticky and validate using php
6. The website should be in compliance with AODA (require descriptive alt tags for all images)
7. Use SQL prepared statements wherever there is user input in the query
8. Use effective techniques for preventing SQL Injection and XSS and other potential threats.
9. Search filter capability for searching products
10. Full shopping cart capability (can add, remove and update products at different quantities).
11. Effective use of Sessions (can store shopping cart in $_SESSION array to preserve shopping cart from page to page).
12. Registration form before checkout if not logged in (userid, name, pw, address, phone, email stored in database - this implies a registration login so we don't have to re-register each time)
13. Login form using a previous registration
14. PDF generated invoice at checkout (includes product list, quantities, product value, total value and name).
15. use the REST API at least once.
16. Should have at least 4 pages.
17. Use appropriate colors and design elements and optimize CSS using bootstrap.

## 🔧 Installation & Deployment

1. Clone the repository and navigate to the project directory:
```bash
git clone https://github.com/jianglei919/ecommerce-goup3.git
cd ecommerce-goup3
```