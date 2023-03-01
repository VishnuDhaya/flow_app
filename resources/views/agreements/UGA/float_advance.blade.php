@php
namespace App\Services;
use App\Repositories\SQL\LoanProductRepositorySQL;
use App\Repositories\SQL\CommonRepositorySQL;
use Log;
use App\Consts;

$hidden_fields = false;

if (empty($borrower)) {
    $hidden_fields = true;
    $borrower = [];
    $borrower['biz_name'] = str_repeat('.', 40);
    $borrower['biz_type'] = str_repeat('.', 40);
    $borrower['cust_name'] = str_repeat('.', 40);
    $borrower['cust_id'] = str_repeat('.', 40);
    $borrower['cust_mobile_num'] = str_repeat('.', 40);
    $borrower['cust_addr_text'] = str_repeat('.', 100);
    $borrower['acc_prvdr_name'] = str_repeat('.', 40);
    $borrower['acc_type'] = str_repeat('.', 40);
    $borrower['acc_number'] = str_repeat('.', 40);
    $borrower['flow_rel_mgr_name'] = str_repeat('.', 20);
    $borrower['flow_rel_mgr_mobile_num'] = str_repeat('.', 20);
    $borrower['national_id'] = str_repeat('.', 20);
    $borrower['category'] = str_repeat('.', 20);
    $borrower = (object) $borrower;
}
$borrower = (object) $borrower;
if (isset($borrower->acc_prvdr_code)) {
    $cs_num = config('app.customer_success')[$borrower->acc_prvdr_code];
} else {
    $cs_num = str_repeat('.', 20);
}
$witness_mobile_num = $witness_mobile_num ?? "";
@endphp

@extends('agreements.UGA.layout')


@section('title')

    <div class=WordSection1>

        <p class=MsoTitle style='margin-top:0in;line-height:normal;'><a name="_p5w1dgewtqn0"></a><span lang=EN-GB>FLOAT
                ADVANCE AGREEMENT</span></p>

        <p class=MsoNormal align=center style='margin-top:6.0pt;text-align:center; line-height:100%'><b><span lang=EN-GB
                    style='font-size:10.0pt;line-height:100%'>{{ $aggr_file_name }}</span></b></p>
@endsection

@section('intro')
        <p class=MsoNormal style='margin-bottom:5.0pt; padding-bottom:2px'>
            <span lang=EN-GB style='font-size:10.0pt;line-height:115%'>This Agreement is made and entered into by <b>Flow Uganda Limited
                (Flow)</b> and <b>{{ $borrower->cust_name }}</b>. Flow is duly licensed under Uganda’s Tier 4
            Microfinance Institutions &amp; Money Lenders Act, 2016. Therefore, this Agreement is subject to and
            enforceable by said Act as well as the Contracts Act, 2010. This Agreement is complemented by
            (electronic-)communications before and after each Float Advance. </span>
        </p>
        <div style="border-top: 0.1px solid; padding: 2px; text-align: center; border-bottom: none;">&nbsp;</div>



@endsection

@section('purpose')

        <p class=MsoNormal style='margin-bottom:0in'><span lang=EN-GB style='font-size:
    6.0pt;line-height:115%'>&nbsp;</span></p>

        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>PURPOSE</span></b></p>

        <p class=MsoNormal style='margin-bottom:6.0pt'>
                <span lang=EN-GB style='font-size:10.0pt;line-height:115%'>The purpose of the Float Advance (FA) is to provide you with <b>float in advance to meet your customer demand for mobile money/payment transactions</b> and thereby to grow your business. Any misuse of the funds towards other purposes shall lead to mismanagement and therefore default. </span></p>
            
        </p>

        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>PRODUCT
                    TERMS</span></b></p>

        <p class=MsoNormal style='margin-bottom:5.0pt; padding-bottom : 2px'>
                <span lang=EN-GB style='font-size:10.0pt;line-height:115%'>The due date along with the amount, duration and fee of each
                specific FA will be communicated electronically. Failing to repay will reduce your ability to get float
                advances in the future and is a breach of the Law - you may be taken to court. </span>
        </p>
@endsection

@section('declaration')

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm
                that I have fully understood the product terms and conditions including
                repayment;</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm
                that I have chosen to take a float advance after careful
                consideration;</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>agree
                for Flow to access and analyse my transaction data to deliver and enhance the
                product; </span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>agree
                that in case I fail to repay on time, Flow can directly debit my account; and</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
    margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
    line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm
                that I understand that in case I fail to repay Flow may pursue legal actions
                against me. </span></p>

        <p class=MsoNormal style='margin-bottom:0in'><span lang=EN-GB style='font-size:
    9.0pt;line-height:115%'>&nbsp;</span></p>

        <p style='margin-bottom:0in'><span lang=EN-GB style='font-size:
    12.0pt;line-height:90%'>&nbsp;</span></p>

        @if (isset($valid_from))
            <p width="100%" style="float: left">
            <p class=MsoNormal><b>Date</b>: {{ format_date($valid_from) }}</p>
            </p>
        @endif

@endsection
