# 🗂️ **TaskFlow**

## 📋 **Overview**

The **Trello Clone** It provides a robust platform for organizing projects using boards, lists, and cards. With **many-to-many relationships**, user authentication, and advanced card management, it offers a seamless experience for project collaboration.

## ✨ **Key Features**

### 🗄️ **Relational Database Design**
- Built a relational database with complex **many-to-many** relationships between **users**, **workspaces**, and **boards**.
- Efficient data handling for projects, ensuring scalability and flexibility.


### 📝 **Comprehensive Card Features**
- Cards include:
  - **Labels** for categorizing tasks.
  - **Comments** for collaboration.
  - **Attachments** (files, images, documents).
  - **Due dates** to manage deadlines.
  - Move/Copy functionality for efficient task management.
  - **Soft delete** for safe deletion and restoration of cards.

### 📊 **Detailed Card Activity Tracking**
- Implemented action logging for all card-related operations:
  - Track **creation, updates, deletions**, and moves.
  - Use complex queries for in-depth analysis of user activities.

### 🏗️ **Service Layer Design Pattern**
- Applied the **service layer pattern** to separate business logic from controllers.
- Improved code maintainability and scalability.

## 🛠️ **Technologies Used**
- **Laravel**: Backend framework for robust task management.
- **JavaScript**, **HTML**, **CSS**: Core web technologies.
- **Eloquent ORM**: For managing database relationships.
- **MySQL**: Relational database management.

