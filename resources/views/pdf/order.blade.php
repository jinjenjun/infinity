<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #333333;
            padding: 40px;
            background: #ffffff;
        }
        .header {
            border-bottom: 3px solid #4b2c25;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #4b2c25;
            letter-spacing: 2px;
        }
        .header p {
            font-size: 12px;
            color: #bbbbbb;
            margin-top: 4px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #4b2c25;
            border-left: 4px solid #4b2c25;
            padding-left: 8px;
            margin-bottom: 12px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-row {
            margin-bottom: 6px;
            font-size: 12px;
        }
        .info-label {
            color: #bbbbbb;
            display: inline-block;
            width: 80px;
        }
        .info-value {
            color: #333333;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-paid { background: #d1fae5; color: #317d00; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-failed { background: #fee2e2; color: #c80000; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr {
            background-color: #4b2c25;
            color: #ffffff;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
        }
        tbody tr {
            border-bottom: 1px solid #eeeeee;
        }
        tbody tr:nth-child(even) {
            background-color: #faf7f6;
        }
        tbody td {
            padding: 10px 12px;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .total-row {
            border-top: 2px solid #4b2c25;
            margin-top: 10px;
            padding-top: 10px;
            text-align: right;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #4b2c25;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #eeeeee;
            padding-top: 16px;
            font-size: 11px;
            color: #bbbbbb;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- 標題 -->
    <div class="header">
        <h1>購物帳單</h1>
        <p>感謝您的購買，以下是您的訂單明細。</p>
    </div>

    <!-- 訂單資訊 + 收件資訊 -->
    <div class="info-grid">
        <div class="info-col">
            <div class="section-title">訂單資訊</div>
            <div class="info-row">
                <span class="info-label">訂單編號</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">建立時間</span>
                <span class="info-value">{{ $order->created_at->format('Y/m/d H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">付款方式</span>
                <span class="info-value">信用卡</span>
            </div>
            <div class="info-row">
                <span class="info-label">訂單狀態</span>
                @if($order->status === 'paid')
                    <span class="status-badge status-paid">已付款</span>
                @elseif($order->status === 'pending')
                    <span class="status-badge status-pending">待付款</span>
                @else
                    <span class="status-badge status-failed">付款失敗</span>
                @endif
            </div>
        </div>

        <div class="info-col">
            <div class="section-title">收件資訊</div>
            <div class="info-row">
                <span class="info-label">收件人</span>
                <span class="info-value">{{ $order->recipient_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">電話</span>
                <span class="info-value">{{ $order->recipient_phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">地址</span>
                <span class="info-value">{{ $order->recipient_address }}</span>
            </div>
        </div>
    </div>

    <!-- 商品明細 -->
    <div class="section-title">商品明細</div>
    <table>
        <thead>
            <tr>
                <th>商品名稱</th>
                <th class="text-right">單價</th>
                <th class="text-right">數量</th>
                <th class="text-right">小計</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-right">NT$ {{ number_format($item->price, 0) }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">NT$ {{ number_format($item->price * $item->quantity, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 合計 -->
    <div class="total-row">
        <span>訂單合計：</span>
        <span class="total-amount">NT$ {{ number_format($order->total, 0) }}</span>
    </div>

    <!-- 頁尾 -->
    <div class="footer">
        本帳單由系統自動產生，如有任何問題請聯繫客服。<br>
        訂單編號：{{ $order->order_number }}
    </div>

</body>
</html>