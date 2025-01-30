<?php
include '../includes/db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch product details
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    // Get product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: manage_products.php?error=Product not found");
        exit();
    }
} else {
    header("Location: manage_products.php?error=Invalid product ID");
    exit();
}

// Update product details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload if a new image is provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../images/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image = $product['image']; // Keep existing image if no new image is uploaded
    }

    // Update product in the database
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    if ($stmt->execute([$name, $price, $description, $image, $product_id])) {
        header("Location: manage_products.php?success=Product updated successfully");
        exit();
    } else {
        header("Location: edit_product.php?id=$product_id&error=Failed to update product");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label for="image">Product Image:</label>
        <input type="file" name="image">
        <p>Current Image: <img src="../images/<?= htmlspecialchars($product['image']); ?>" width="50"></p>

        <button type="submit">Update Product</button>
    </form>
</div>

</body>
</html>
