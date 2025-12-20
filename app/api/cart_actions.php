<?php
// Use output buffering to catch any stray output
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
// ... (rest of the file is unchanged but will not be reached)


header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/CartItemModel.php';

$response = ['success' => false, 'message' => 'Hành động không hợp lệ.'];
$action = $_REQUEST['action'] ?? '';
$productModel = new ProductModel($conn);

// Check if a user is logged in
if (isset($_SESSION['user_id'])) {
    /************************************************/
    /* LOGIC FOR LOGGED-IN USERS (DATABASE-BACKED)  */
    /************************************************/
    
    $userId = $_SESSION['user_id'];
    $cartModel = new CartModel($conn);
    $cartItemModel = new CartItemModel($conn);

    // Get the user's active cart, or create one if it doesn't exist
    try {
        $cart = $cartModel->getOrCreateActiveCartByUserId($userId);
        if (!$cart) {
            throw new Exception("Không thể tạo hoặc tìm thấy giỏ hàng.");
        }
        $cartId = $cart['id'];
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    switch ($action) {
        case 'add':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($productId > 0 && $quantity > 0) {
                $product = $productModel->getById($productId);
                if ($product) {
                    $price = (isset($product['discount_price']) && $product['discount_price'] > 0) ? $product['discount_price'] : $product['price'];
                    if ($cartItemModel->addOrUpdateItem($cartId, $productId, $quantity, $price)) {
                        $response['success'] = true;
                        $response['message'] = 'Sản phẩm đã được thêm vào giỏ hàng!';
                    } else {
                        $response['message'] = 'Không thể thêm sản phẩm vào giỏ hàng.';
                    }
                } else {
                    $response['message'] = 'Sản phẩm không tồn tại.';
                }
            } else {
                $response['message'] = 'Dữ liệu sản phẩm không hợp lệ.';
            }
            break;

        case 'update':
            $cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

            if ($cartItemId > 0 && $quantity > 0) {
                if ($cartItemModel->updateItemQuantity($cartItemId, $quantity)) {
                    $response['success'] = true;
                    $response['message'] = 'Cập nhật số lượng thành công!';
                } else {
                    $response['message'] = 'Không thể cập nhật số lượng.';
                }
            } else {
                $response['message'] = 'Dữ liệu không hợp lệ.';
            }
            break;
        
case 'remove':
            // Logic now consistently uses the session for logged-in users, matching the cart display.
            $productId = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
            
            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                $response['success'] = true;
                $response['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
            } else {
                $response['message'] = 'Sản phẩm không có trong giỏ hàng hoặc ID không hợp lệ.';
            }
            break;
        
        case 'get_cart_data':
            $items_result = $cartItemModel->getItemsByCartId($cartId);
            $cart_products = [];
            $cart_total_price = 0;
            $item_count = 0;
            
            if ($items_result) {
                while ($item = $items_result->fetch_assoc()) {
                     $sub_total = $item['price'] * $item['quantity'];
                     $cart_total_price += $sub_total;
                     $cart_products[] = $item;
                }
                $item_count = $cartItemModel->getItemCountByCartId($cartId);
            }
            
            $response['success'] = true;
            $response['message'] = 'Lấy dữ liệu giỏ hàng thành công.';
            $response['item_count'] = $item_count;
            $response['total_price_formatted'] = number_format($cart_total_price, 0, ',', '.') . '₫';
            $response['cart_products'] = $cart_products;
            break;
    }

} else {
    /***************************************/
    /* LOGIC FOR GUEST USERS (SESSION-BASED) */
    /***************************************/

    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    switch ($action) {
        case 'add':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($productId > 0 && $quantity > 0) {
                $product = $productModel->getById($productId);
                if ($product) {
                    $price = (isset($product['discount_price']) && $product['discount_price'] > 0) ? $product['discount_price'] : $product['price'];
                    
                    if (isset($_SESSION['guest_cart'][$productId])) {
                        // Product exists, update quantity
                        $_SESSION['guest_cart'][$productId]['quantity'] += $quantity;
                    } else {
                        // Product does not exist, add it
                        $_SESSION['guest_cart'][$productId] = [
                            'product_id' => $productId,
                            'name' => $product['name'],
                            'thumbnail' => $product['thumbnail'],
                            'quantity' => (int)$quantity,
                            'price' => $price
                        ];
                    }
                    $response['success'] = true;
                    $response['message'] = 'Sản phẩm đã được thêm vào giỏ hàng!';
                } else {
                    $response['message'] = 'Sản phẩm không tồn tại.';
                }
            } else {
                $response['message'] = 'Dữ liệu sản phẩm không hợp lệ.';
            }
            break;

        case 'update':
            // Note: For guest cart, we use product_id instead of cart_item_id
            $productId = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : 0;
            $quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 0;

            if ($productId > 0 && isset($_SESSION['guest_cart'][$productId])) {
                if ($quantity > 0) {
                    $_SESSION['guest_cart'][$productId]['quantity'] = (int)$quantity;
                    $response['success'] = true;
                    $response['message'] = 'Cập nhật số lượng thành công!';
                } else {
                    // If quantity is 0 or less, remove the item
                    unset($_SESSION['guest_cart'][$productId]);
                    $response['success'] = true;
                    $response['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                }
            } else {
                $response['message'] = 'Sản phẩm không có trong giỏ hàng hoặc dữ liệu không hợp lệ.';
            }
            break;

        case 'remove':
            // Note: For guest cart, we use product_id instead of cart_item_id
            $productId = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : 0;
            if ($productId === 0) {
                $productId = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0; // Fallback for 'id'
            }

            if ($productId > 0 && isset($_SESSION['guest_cart'][$productId])) {
                unset($_SESSION['guest_cart'][$productId]);
                $response['success'] = true;
                $response['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
            } else {
                $response['message'] = 'Sản phẩm không có trong giỏ hàng hoặc dữ liệu không hợp lệ.';
            }
            break;
            
        case 'get_cart_data':
            $cart_products = array_values($_SESSION['guest_cart']); // Re-index array
            $cart_total_price = 0;
            $item_count = 0;
            
            foreach ($cart_products as $item) {
                 $sub_total = $item['price'] * $item['quantity'];
                 $cart_total_price += $sub_total;
                 $item_count += $item['quantity'];
            }
            
            $response['success'] = true;
            $response['message'] = 'Lấy dữ liệu giỏ hàng thành công.';
            $response['item_count'] = count($cart_products);
            $response['total_price_formatted'] = number_format($cart_total_price, 0, ',', '.') . '₫';
            $response['cart_products'] = $cart_products;
            break;
    }
}

ob_end_clean(); // Clean the buffer and end output buffering
echo json_encode($response);

