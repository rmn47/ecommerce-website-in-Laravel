<?php

/*
|--------------------------------------------------------------------------
| preorder Routes
|--------------------------------------------------------------------------
|
| Here is where you can register preorder routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Preorder\DashboardController;
use App\Http\Controllers\Preorder\FaqController;
use App\Http\Controllers\Preorder\NotificationTypeController;
use App\Http\Controllers\Preorder\OrderController;
use App\Http\Controllers\Preorder\PreorderCommissionHistoryController;
use App\Http\Controllers\Preorder\PreorderController;
use App\Http\Controllers\Preorder\PreorderConversationController;
use App\Http\Controllers\Preorder\PreorderProductController;
use App\Http\Controllers\Preorder\PreorderProductQueryController;
use App\Http\Controllers\Preorder\PreorderProductReviewController;
use App\Http\Controllers\Preorder\ProductController;
use App\Http\Controllers\Preorder\seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Preorder\seller\OrderController as SellerOrderController;
use App\Http\Controllers\Preorder\seller\PreorderCommissionHistoryController as SellerPreorderCommissionHistoryController;
use App\Http\Controllers\Preorder\seller\PreorderController as SellerPreorderController;
use App\Http\Controllers\Preorder\seller\PreorderConversationController as SellerPreorderConversationController;
use App\Http\Controllers\Preorder\seller\PreorderProductController as SellerPreorderProductController;
use App\Http\Controllers\Preorder\seller\PreorderProductQueryController as SellerPreorderProductQueryController;
use App\Http\Controllers\Preorder\seller\PreorderProductReviewController as SellerPreorderProductReviewController;


Route::group([ 'middleware' => ['isPreorder', ]], function () {

    // Admin Routes
    Route::group(['prefix' => 'admin/preorder', 'middleware' => ['auth', 'admin', 'prevent-back-history']], function () {
        
        // Admin Dashboard
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('preorder.dashboard');
            Route::post('/dashboard/preorder-by-products-section', 'preorderByProductsSection')->name('dashboard.preorder-by-products-section');
        });

        // Preorder Products
        Route::resource('preorder-product', PreorderProductController::class);
        Route::controller(PreorderProductController::class)->group(function () {
            Route::get('/preorder-product/edit/{id}', 'edit')->name('preorder-product.edit');
            Route::get('/preorder-product/destroy/{id}', 'destroy')->name('preorder-product.destroy');
            Route::post('/preorder-product/bulk-destroy', 'bulkProductDestroy')->name('preorder-product.bulk-destroy');
            Route::post('/preorder-product/published', 'preorder_product_published')->name('preorder-product.published');
            Route::post('/preorder-product/approval', 'preorderProductApproval')->name('preorder-product.approval');
            Route::post('/preorder-product/featured', 'preorder_product_featured')->name('preorder-product.featured');
            Route::post('/preorder-product/show-on-homepage', 'preorder_product_show_on_homepage')->name('preorder-product.show_on_homepage');
        });

        // Preorders
        Route::controller(OrderController::class)->group(function () {
            Route::get('/all-order-list', 'order_list')->name('all_preorder.list');
            Route::get('/inhouse-order-list', 'order_list')->name('inhouse_preorder.list');
            Route::get('/seller-order-list', 'order_list')->name('seller_preorder.list');
            Route::get('/delayed-prepayment-preorders-list', 'order_list')->name('delayed_prepayment_preorders.list');
            Route::get('/delayed-final-orders-list', 'order_list')->name('delayed_final_orders.list');
            
            Route::get('/order/{id}', 'show')->name('preorder-order.show');
            Route::get('/order/destroy/{id}', 'destroy')->name('preorder-order.destroy');
            Route::post('/bulk-preorder-delete', 'bulkPreorderDelete')->name('bulk-preorder-delete');
            Route::put('/order/update/{id}', 'order_status_update')->name('preorder-order.status_update');
            Route::post('prepayment-final-preorder-reminder', 'prepaymentFinalPreorderReminder')->name('prepayment_final_preorder_reminder');
        });
        
        Route::controller(PreorderController::class)->group(function() {
            Route::get('/preorder-settings', 'preorderSettings')->name('preorder-settings');
        });

        // Seller Commission History
        Route::controller(PreorderCommissionHistoryController::class)->group(function() {
            Route::get('preorder-commission-history', 'index')->name('preorder-commission-history');
        });
        
        //conversation of Product owner & customer
        Route::controller(PreorderConversationController::class)->group(function () {
            Route::get('conversations', 'adminIndex')->name('preorder-conversations.admin_index');
            Route::get('conversations/{id}/show', 'adminShow')->name('preorder-conversations.admin_show');
            Route::post('conversations/message-reply', 'messageReply')->name('preorder-conversations.admin_reply');
            Route::get('conversations/destroy/{id}', 'conversationDestroy')->name('preorder-conversations.destroy');
        });

        // FAQs
        Route::resource('faqs', FaqController::class);
        Route::controller(FaqController::class)->group(function () {
            Route::get('/faq/edit/{id}', 'edit')->name('faq.edit');
            Route::post('/faq/update-status', 'updateStatus')->name('faq.update-status');
            Route::get('/faq/destroy/{id}', 'destroy')->name('faq.destroy');
        });

        // product Queries show on Admin panel
        Route::controller(PreorderProductQueryController::class)->group(function () {
            Route::get('/preorder-product-queries', 'index')->name('preorder.product_query.index');
            Route::get('/preorder-product-queries/{id}', 'show')->name('preorder.product_query.show');
            Route::put('/preorder-product-queries/{id}', 'reply')->name('preorder.product_query.reply');
        });

        // Product Reviews
        Route::controller(PreorderProductReviewController::class)->group(function () {
            Route::get('/preorder-product-reviews', 'adminIndex')->name('preorder.product_reviews.index');
            Route::post('/preorder-product/reviews/update-status', 'updateStatus')->name('preorder.product_reviews.update_status');
            Route::get('/preorder-product/reviews/detail-reviews/{id}', 'detailReviews')->name('preorder.product_detail_reviews');
        });

        // Notification Types 
        Route::resource('preorder-notification-types', NotificationTypeController::class);
        Route::controller(NotificationTypeController::class)->group(function () {
            Route::get('/preorder-notification/edit/{id}', 'edit')->name('preorder.notification-type.edit');
        });
        
    });

    // Seller Routes
    Route::group(['prefix' => 'seller/preorder', 'middleware' => ['seller', 'verified', 'user'], 'as' => 'seller.'], function() {

        Route::controller(SellerDashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('preorder.dashboard');
        });

        Route::controller(SellerPreorderController::class)->group(function() {
            Route::get('/preorder-settings', 'preorderSettings')->name('preorder-settings');
            Route::post('/preorder-instruction-update', 'updatePreorderInstruction')->name('preorder-instruction-update');
        });

        Route::resource('preorder-product', SellerPreorderProductController::class);  
        Route::controller(SellerPreorderProductController::class)->group(function () {
            Route::get('/preorder-product/edit/{id}', 'edit')->name('preorder-product.edit');
            Route::get('/preorder-product/destroy/{id}', 'destroy')->name('preorder-product.destroy');
            Route::post('/preorder-product/bulk-destroy', 'bulkProductDestroy')->name('preorder-product.bulk-destroy');
            Route::post('/preorder-product/published', 'preorder_product_published')->name('preorder-product.published');
            Route::post('/preorder-product/featured', 'preorder_product_featured')->name('preorder-product.featured');
        });  

        Route::controller(SellerOrderController::class)->group(function () {
            Route::get('/all-order-list', 'index')->name('all_preorder.list');
            Route::get('/delayed-prepayment-preorders-list', 'index')->name('delayed_prepayment_preorders.list');
            Route::get('/delayed-final-orders-list', 'index')->name('delayed_final_orders.list');
            

            // Route::get('order-list', 'index')->name('preorders.index');
            Route::get('/order/{id}', 'show')->name('preorder-order.show');
            Route::get('/order/destroy/{id}', 'destroy')->name('preorder-order.destroy');
            Route::post('/bulk-preorder-delete', 'bulkPreorderDelete')->name('bulk-preorder-delete');
            Route::put('/order/update/{id}', 'orderStatusUpdate')->name('preorder-order.status_update');
        });

        // Seller Commission History
        Route::controller(SellerPreorderCommissionHistoryController::class)->group(function() {
            Route::get('preorder-commission-history', 'index')->name('preorder-commission-history');
        });

        // product query (comments) show on seller panel
        Route::controller(SellerPreorderProductQueryController::class)->group(function () {
            Route::get('/product-queries', 'index')->name('preorder_product_query.index');
            Route::get('/product-queries/{id}', 'show')->name('preorder_product_query.show');
            Route::put('/product-queries/{id}', 'reply')->name('preorder_product_query.reply');
        });

        Route::controller(SellerPreorderProductReviewController::class)->group(function () {
            Route::get('/product-reviews', 'index')->name('preorder_product_reviews');
            Route::get('/product/detail-reviews/{id}', 'detailReviews')->name('preorder_product_detail_reviews');
        });

        //conversation of Product owner & customer
        Route::controller(SellerPreorderConversationController::class)->group(function () {
            Route::get('conversations', 'index')->name('preorder-conversations.index');
            Route::get('conversations/{id}/show', 'show')->name('preorder-conversations.show');
            Route::post('conversations/message-reply', 'messageReply')->name('preorder-conversations.reply');
            Route::get('conversations/destroy', 'messageReply')->name('preorder-conversations.destroy');
        });
    });

    // frontend
    Route::group(['prefix' => 'preorder'], function () {
    Route::get('product/{slug}',[ProductController::class,'product_details'])->name('preorder-product.details');

    Route::group(['middleware' => ['user', 'verified', 'unbanned']], function () {
            Route::controller(PreorderController::class)->group(function () {
                Route::post('/updateDeliveryAddress', 'updateDeliveryAddress')->name(name: 'updateDeliveryAddress');
                Route::post('/place-order', 'place_order')->name('preorder.place_order');
                Route::get('/order-list', 'order_list')->name('preorder.order_list');
                Route::get('/order-details/{id}', 'order_details')->name('preorder.order_details');
                Route::put('/order-details/{id}', 'order_update')->name('preorder.order_update');
                Route::post('/apply-coupon-code', 'apply_coupon_code')->name('preorder.apply_coupon_code');
                Route::post('/remove-coupon-code', 'remove_coupon_code')->name('preorder.remove_coupon_code');
            });

            // Product Review
            Route::resource('/preorder-product-reviews', PreorderProductReviewController::class);
            Route::post('/product-review-modal', [PreorderProductReviewController::class, 'product_review_modal'])->name('preorder.product_review_modal');

            // Conversation
            Route::controller(PreorderConversationController::class)->group(function () {
                Route::post('/conversation-modal', 'preorderConversationModal')->name('preorder.conversation_modal');
                Route::post('/conversation-store', 'store')->name('preorder.conversations.store');
                Route::get('/conversations', 'customerIndex')->name('preorder-conversations.customer-index');
                Route::get('/conversation/{id}/show', 'customerShow')->name('preorder-conversations.customer-show');
                Route::post('/conversation/refresh', 'refresh')->name('preorder.conversations.refresh');
                Route::post('/conversations/message-reply', 'messageReplyCustomer')->name('preorder-conversations.customer_reply');
                
            });
        });
    });

    // Common Routes
    Route::group(['prefix' => 'preorder', 'middleware' => ['auth']], function () {
        Route::controller(PreorderProductController::class)->group(function () {
            Route::post('/preordr-product/search', 'product_search')->name('preorder_product.search');
            Route::post('/get-selected-preorder-products', 'get_selected_products')->name('get-selected-preorder-products');
        });

        // Product Query
        Route::resource('preorder-product-queries', PreorderProductQueryController::class);
        Route::get('/invoice-download/{id}', [OrderController::class,'invoice_download'])->name('preorder.invoice_download');
        Route::get('/invoice-preview/{id}', [OrderController::class,'invoice_preview'])->name('preorder.invoice_preview');
    });

    // guest routes for preorder
    Route::get('/all-preorder-products', [PreorderProductController::class,'all_preorder_products'])->name('all_preorder_products');
    Route::get('/preorder/category/{category_slug}', [PreorderProductController::class,'listingByCategory'])->name('preorder.category');
    Route::get('/how-to-preorder', [PreorderProductController::class,'how_to_preorder'])->name('how_to_preorder');
});