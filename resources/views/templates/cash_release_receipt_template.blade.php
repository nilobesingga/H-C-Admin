<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="{{ public_path('css/bootstrap-print.min.css') }}" media="all" />
        <link rel="stylesheet" href="{{ public_path('css/print-invoice.css') }}" media="all" />
    </head>
    <body>
        <main>
            <div  class="container default-font" >
                <div class="row">
                    <div class="col-xs-6" >
                        <div class="mb-2 text-align-right">
                            <img class="h-logo" src="{{$logo}}" style="width: 300px;">
                        </div>
                    </div>
                </div>
                <div class="mt-10">
                    <div class="row text-center">
                        <h2>CASH REQUEST RECEIPT</h2>
                    </div>
                </div>
                <div class="mt-10">
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="row">
                                <div class="col-xs-4 mt-2" style="font-weight: bold">Request Id:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro"> {{$requestId}}</div>
                                <div class="col-xs-4 mt-2" style="font-weight: bold">Release Type:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">
                                    @if ($cashReleaseType == 1)
                                        Full Cash Release
                                    @else
                                        Partial Cash Release
                                    @endif
                                </div>

                                @if ($cashReleaseType == 2)
                                    <div class="col-xs-4 mt-2" style="font-weight: bold">Remaining Balance:</div>
                                    <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">
                                        {{number_format($balance,2)}} {{$currency}}
                                    </div>
                                @endif

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Date Requested:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro"> {{$requestCreateDate}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Payment Date:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro"> {{$requestPaymentDate}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Payment Mode:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro"> {{$paymentMode}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Date Received:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro"> {{$releaseDate}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Requested By:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">{{$requestedBy}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Released By:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">{{$releasedBy}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Project:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">{{$project}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Company:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">{{$company}}</div>

                                <div class="col-xs-4 mt-2" style="font-weight: bold">Remarks:</div>
                                <div class="col-xs-8 mt-2" style="border-bottom:1px solid gainsboro">{{$remarks}}</div>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="row" style="height: 100%">
                                <div class="col-xs-12 text-center">
                                    <div style="height: 103px; color: white; background-color: #860f00">
                                        <div style="padding-top: 25px;">Amount Received</div>
                                        <div style="font-size: 21px;font-weight: bold;">   {{number_format($amountReceived,2)}}  {{$currency}} </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-15">
                    <div class="row">
                        <div class="col-xs-8">
                        </div>
                        <div class="col-xs-4" style="border-top: 2px solid black; text-align:center">
                            SIGNATURE
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
