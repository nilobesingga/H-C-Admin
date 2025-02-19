<div>
    <div style="height:50px;padding-top:2rem">
            <div class="juridiction-container" style=" float:left">
                @if($countryOfIncorporation == 'United Arab Emirates')
                    <span class="jurisdiction-header">UAE</span>
                @elseif($countryOfIncorporation == 'Seychelles')
                    <span class="jurisdiction-header">SEZ</span>
                @endif

            </div>
            <div class="logo">
                <img src="{{public_path('img/Hensley.png')}}" style="width: 200px;">
            </div>
    </div>

    <div class="text-center orange" style="margin-bottom:2rem">
        <h1>{{$title}}</h1>
    </div>

    <div style="height: 60px;">
        <div class="float-left" style="width: 60%">
                <div class="label">Company Name</div>
                <div style="font-size: 24px; font-weight: bold; margin-top: -6px;">{{$companyName}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div  class="label">Registry Number</div>
                <div>{{$registryNumber}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div class="label">Incorporation Date</div>
                <div>{{$incorporationDate}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div class="label">Tin</div>
                <div>{{ $tin }}</div>
        </div>

    </div>

    <div style="height: 50px;">
        <div class="float-left" style="width: 60%">
                <div class="label">Registered Address</div>
                <!-- <div>{!! nl2br($registeredAddress) !!}</div> -->
                <div style="max-width: 50rem;">{{$registeredAddress}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div class="label">Organisation Type</div>
                <div>{{$organizationType}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div  class="label">Registrar / Jurisdiction</div>
                <div>{{$authority}}</div>
        </div>
        <div class="item2 float-left" style="width: 13.33%">
                <div  class="label">Status</div>
                <div>{{ $companyStatusText}}</div>
        </div>

    </div>
</div>
