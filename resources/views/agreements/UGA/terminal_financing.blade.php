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
    $borrower['duration_in_months'] = str_repeat('.', 20);
    $borrower['loan_amount'] = str_repeat('.', 20);
    $borrower['flow_fee'] = str_repeat('.', 20);
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

        <p class=MsoTitle style='margin-top:0in;line-height:normal;'><a name="_p5w1dgewtqn0"></a><span
                lang=EN-GB>POS-TERMINAL FINANCING LOAN AGREEMENT</span></p>

@endsection

@section('intro')

        <p class=MsoNormal style='margin-bottom:2.0pt; padding-bottom:2px'><span lang=EN-GB
                style='font-size:10.0pt;line-height:115%'>This Agreement is made and entered into by <b>Flow Uganda
                    Limited (Flow)</b> and <b>{{ $borrower->cust_name }}</b>. Flow is duly licensed under Uganda’s Tier
                4 Microfinance Institutions & Money Lenders Act, 2016. Therefore, this Agreement is subject to and
                enforceable by said Act as well as the Contracts Act, 2010. </span></p>
        <div style="border-top: 0.1px solid; text-align: center;margin-bottom:0pt; border-bottom: none;">&nbsp;</div>
@endsection

 
@section('purpose')
        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>PURPOSE</span></b></p>

        <p class=MsoNormal style='margin-bottom:6.0pt'><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>The
                purpose of the Terminal Financing (TF) is to provide you with a loan for the purchase of a POS-terminal.
                The POS-terminal will enable you to offer mobile money and payment transactions and thereby to grow your
                business. </span></p>

        <p class=MsoNormal style='margin-bottom:0in'><b><span lang=EN-GB>LOAN PRODUCT TERMS
                </span></b></p>

        <p class=MsoNormal style='margin-bottom:5.0pt; padding-bottom : 2px'><span lang=EN-GB
                style='font-size:10.0pt;line-height:115%'>The POS-Terminal value is 1,300,000 UGX (One million three
                hundred thousand Ugandan Shillings Only). For the purchase of the POS Terminal you will have to
                contribute 500,000 UGX (Five Hundred Thousand Ugandan Shillings Only) in the form of a down payment and
                Flow will offer a loan of 800,000 UGX (Eight Hundred Thousand Ugandan Shillings Only). You will have to
                repay the loan along with the interest as described below. Installments will be deducted daily and
                automatically from the Terminal’s Float Account that will be created for you.
            </span></p>
        <table width=100% cellspacing=0 cellpadding=0>
            <tbody>
                <tr>
                    <td class="c41 c37" rowspan="1">
                        <p class="c9"><span class="c5 c0">Loan Amount</span></p>
                    </td>
                    <td class="c26 c37" rowspan="1">
                        <p class="c9"><span class="c5 c0">Loan Duration </span></p>
                    </td>
                    <td class="c37 c45" rowspan="1">
                        <p class="c9"><span class="c5 c0">Total Fee</span></p>
                    </td>
                    <td class="c34 c37" rowspan="1">
                        <p class="c9"><span class="c5 c0">Repayment Cycle</span></p>
                    </td>
                </tr>
                <tr class="c36">
                    <td class="c41" colspan="1" rowspan="1">
                        <p class="c9"><span class="c5 c6 c4">{{ $borrower->loan_amount }}</span></p>
                    </td>
                    <td class="c45" rowspan="1">
                        <p class="c9"><span
                                class="c5 c6 c4">{{ $borrower->duration_in_months }}</span></p>
                    </td>
                    <td class="c26" rowspan="1">
                        <p class="c9"><span class="c5 c6 c4">{{ $borrower->flow_fee }}</span></p>
                    </td>
                    <td class="c34" rowspan="1">
                        <p class="c9"><span class="c5 c6 c4">Deducted daily (through auto
                                debit)</span></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class=MsoNormal style='margin-bottom:6.0pt'><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>The
                Terminal Financing Loan Product shall be bundled with Float Advances (FAs). The purpose of Float
                Advances is to help you grow your mobile money/payment business. You shall take at least 2 FAs per month
                for the first three months after which you may opt out of the FA service and continue with the POS
                Terminal.
            </span></p>
        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>You authorise Flow to debit the
                EzeeMoney Float Account (Service Centre-SC) linked to the POS-Terminal as per the terms above.
            </span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>Failure to repay by not keeping
                enough balance will be deemed a default which may result in Flow blocking the POS-terminal and/or
                retrieving the POS-terminal and is also a breach of the Law which will lead to debt recovery process
                through courts of law of Uganda.
            </span></p>

@endsection

@section('declaration')

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm
                that I have fully understood the product terms and conditions including
                repayment.</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm that I have chosen to
                take this loan for a POS-terminal (SC) along with Float Advances(FA)
                after careful consideration.
            </span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>agree
                for Flow to access and analyse my transaction data to deliver and enhance the
                product. </span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>agree
                that in case I fail to repay on time, Flow can directly debit my account.</span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>agree that in case I fail to
                keep enough balance each day for Flow to auto debit,
                Flow shall block the POS-terminal and confiscate the POS-terminal from me.
            </span></p>

        <p class=MsoNormal style='margin-top:0in;margin-right:0in;margin-bottom:0in;
margin-left:.5in;text-indent:-.25in'><span lang=EN-GB style='font-size:10.0pt;
line-height:115%'>-<span style='font:7.0pt '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </span></span><span lang=EN-GB style='font-size:10.0pt;line-height:115%'>confirm
                that I understand that in case I fail to repay Flow may pursue legal actions
                against me. </span></p>


        <p style='margin-bottom:0in;margin-top:0in'><span lang=EN-GB style='font-size:
12.0pt;line-height:10%'>&nbsp;</span></p>
        @if (isset($valid_from))
            <p width="100%" style="float: left">
            <p class=MsoNormal><b>Date</b>: {{ format_date($valid_from) }}</p>
            </p>
        @endif


@endsection




