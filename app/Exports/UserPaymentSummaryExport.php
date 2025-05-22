<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Payment;
use App\Models\PaymentItem;

class UserPaymentSummaryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $selected_rider, $selected_product_type, $selected_payment_status, $start_date, $end_date;

    public function __construct($selected_rider, $selected_product_type, $selected_payment_status, $start_date, $end_date)
    {
        $this->selected_rider = $selected_rider;
        $this->selected_product_type = $selected_product_type;
        $this->selected_payment_status = $selected_payment_status;
        $this->start_date = $start_date;
    }
    public function collection()
    {
       $data = Payment::when($this->selected_rider, function ($query) {
            $query->where('user_id', $this->selected_rider);
        })
        ->when($this->selected_product_type, function ($query) {
            $query->where('order_type', $this->selected_product_type);
        })
        ->when($this->selected_payment_status, function ($query) {
            $query->where('payment_status', $this->selected_payment_status);
        })
        ->when($this->start_date && $this->end_date, function ($query) {
            $query->whereBetween('payment_date', [$this->start_date. ' 00:00:00', $this->end_date . ' 23:59:59']);
        })
        ->when($this->start_date && !$this->end_date, function ($query) {
            $query->whereDate('payment_date', '>=', $this->start_date);
        })
        ->when(!$this->start_date && $this->end_date, function ($query) {
            $query->whereDate('payment_date', '<=', $this->end_date);
        })
        ->orderBy('id', 'DESC')
        ->get()->map(function ($payment) {
        return [
            'user_name' => $payment->user ? $payment->user->name : 'N/A', // User Name
            'order_type' => ucwords(str_replace('_', ' ', $payment->order_type)),
            'payment_method' => ucwords($payment->payment_method),
            'payment_status' => ucwords($payment->payment_status),
            'razorpay_order_id' => $payment->razorpay_order_id,
            'razorpay_payment_id' => $payment->razorpay_payment_id,
            'ride_duration' => optional(PaymentItem::where('payment_id', $payment->id)->first())->duration 
            ? optional(PaymentItem::where('payment_id', $payment->id)->first())->duration . ' Days' 
            : '0 Days',
            'currency' => $payment->currency,
            'rental_amount' => PaymentItem::where('payment_id', $payment->id)
            ->where('type', 'rental')
            ->first()?->amount ?? 0,
            'deposit_amount' => PaymentItem::where('payment_id', $payment->id)
            ->where('type', 'deposit')
            ->first()?->amount ?? 0,
            'total_amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
        ];
    })
    ->toArray();
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Rider Name',
            'Order Type',
            'Payment Method',
            'Payment Status',
            'Razorpay Order ID',
            'Razorpay Payment ID',
            'Subscription Duration',
            'Currency',
            'Rental Amount',
            'Deposit Amount',
            'Total Amount',
            'Payment Date',
        ];
    }
}
