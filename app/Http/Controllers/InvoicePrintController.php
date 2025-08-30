<?php

namespace App\Http\Controllers;

use App\Models\{Invoice, InvoiceItem, Payment, User, UserProfile, Organization, PartnerOffering, Package, UserOffering, UserAffiliation, LedgerEntry};
use Illuminate\Http\Request;

class InvoicePrintController extends Controller
{
    public function show(Invoice $invoice)
    {
        $user     = User::find($invoice->user_id);
        $profile  = UserProfile::where('user_id', $invoice->user_id)->latest('id')->first();
        $items    = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $payments = Payment::where('invoice_id', $invoice->id)->get();

        return view('invoices.print', compact('invoice','user','profile','items','payments'));
    }
}
