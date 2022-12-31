<?php
namespace App\Http\Services\Payment;



class SepPaymentService
{
    protected int $amount;
    protected string $MID;
    protected int $ResNum;
    protected string $RedirectUrl;


    public function __construct($amount, $MID, $ResNum, $RedirectUrl)
    {
        $this->amount = $amount;
        $this->MID = $MID;
        $this->ResNum = $ResNum;
        $this->RedirectUrl = $RedirectUrl;
    }

    public function payment()
    {
        $AMOUNT = $this->amount;
        $MID = $this->MID;
        $ResNumber = $this->ResNum;
        $RedirectUrl = $this->RedirectUrl;


        $client = new \soapclient('https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL');
        $result = $client->RequestToken(
            $MID,		   /// MID
            $ResNumber, 	   /// ResNum
            $AMOUNT, 	   /// TotalAmount
            $RedirectUrl   /// RedirectURL
        );


        echo "<form style='text-align:center; margin-top:5rem;' action='https://sep.shaparak.ir/payment.aspx' method='POST'>
				<input name='token' type='hidden' value='".$result."'>
				<input name='RedirectURL' type='hidden' value=$RedirectUrl>
				<input name='btn' style='color: #fff;
                    background: #4c0080;
                    padding: 3rem;
                    font-size: 20px;
                    border-radius: 16px;
                    border: none;
                    cursor: pointer;'


                type='submit' value='برای رفتن به درگاه بانک سامان کلیک نمایید'>
			</form>";
    }

    public function verify($request, $redirect)
    {
        $req = $request->all();
        $Verify_URL=new \soapclient('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL');

        $res= $Verify_URL->verifyTransaction($req['RefNum'],$this->MID);
        if( $res <= 0 )
        {
            return redirect()->route($redirect)->with('error', 'Your payment was unsuccessful');
        }
    }
}
