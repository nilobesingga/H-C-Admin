<!DOCTYPE html>
<meta charset="UTF-8">

<html>
<head>
    <!-- <link rel="stylesheet" href="{{public_path('css/bootstrap-print.min.css')}}" media="all" /> -->
    <!-- <link rel="stylesheet" href="{{public_path('css/print-invoice.css')}}" media="all" />  -->
</head>

<style>
    body {
        font-family: Nunito, sans-serif;
        font-size: .9rem;
        font-weight: 400;
        line-height: 1.6;
        color: #212529;
    }

    .orange {
        color: #c63;
    }

    .jurisdiction-header {
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-size: 20px;
        color: white;
        background-color: #cc6533;
        padding: 10px 10px 6px 10px;
        font-weight: bold;
    }


    .logo {
        float: right
    }

    .text-center {
        text-align: center;
    }

    .float-right {
        float: right;
    }

    .float-left {
        float: left;
    }

    .label {
        font-size: 13px;
        height: 20px;
        color: #6c757d !important;
    }

    .header-labels {
        color: #c63;
        font-weight: bold;
        font-size: 20px;
        padding: 0px !important;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-collapse: collapse;
        display: table;
        text-indent: initial;
        border-spacing: 2px;
        border-color: grey;
        border-top: 3px solid #cc6634;
        page-break-inside: auto;
    }

    thead {
        display: table-header-group;
        vertical-align: middle;
        border-color: inherit;
    }

    tr {
        display: table-row;
        vertical-align: inherit;
        border-color: inherit;
        page-break-inside: avoid;
        page-break-after: auto
    }

    .table thead th {
        font-size: 10px;
        border: 1px solid;
        vertical-align: middle;
        padding: 0.75rem;
    }

    tbody {
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
    }

    td {
        border: 1px solid;
        padding: 0.5em;
        white-space: wrap;
        overflow: visible;
    }

    .break {
        page-break-after: always;
        page-break-inside: avoid;
    }

    .nominee {
        font-size: 12px;
    }

    .address {
        font-size: 11px;
        color: gray
    }

    .multiline {
        white-space: pre-line;
    }

    .avoid-break {
        page-break-inside: avoid;
    }


</style>

<body>


<main>

    <div class="container default-font break">
        <x-compliance-header
            :companyName="$company_name"
            :registryNumber="$registry_number"
            :incorporationDate="$incorporation_date"
            :registeredAddress="$registered_address"
            :organizationType="$organization_type"
            :authority="$authority"
            :title="'Register of Directors'"
            :countryOfIncorporation="$country_of_incorporation"
            :tin="$tin ?? 'Not Provided'"
            :companyStatusText="$status"
        />
        <div>
            <table class="table" style="margin-top:2rem">
                <thead>
                <tr class="break">
                    <th style="width:300px;">Name</th>
                    <th style="width:300px;">Address</th>
                    <th style="width:100px;">Date of Birth / Incorporation</th>
                    <th style="width:100px;">Nationality / Place of Incorporation</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:100px;">Appointed</th>
                    <th style="width:100px;">Resigned</th>
                </tr>
                </thead>
                <tbody>
                @foreach($directors as $director)
                    <tr>
                        <td>{{isset($director['name']) ? $director['name']: '' }}</td>
                        <td>{{isset($director['address']) ? $director['address']: '' }}</td>
                        <td>{{ isset($director['dateOfBirth'])
                                        ? $director['dateOfBirth']
                                        : (isset($director['incorporationDate']) ? $director['incorporationDate'] : '') }}
                        </td>
                        <td>{{isset($director['nationality'])
                                        ? $director['nationality']
                                        : (isset($director['jurisdiction']) ? $director['jurisdiction'] : '')}}
                        </td>
                        <td>Director</td>
                        <td class="text-center">
                            @if(isset($director['startDate']) && $director['startDate'] != null)
                                {{ date("d M Y", strtotime($director['startDate'])) }}
                            @endif
                            {{--                                    @php echo date("d M Y", strtotime($director['startDate'] )); @endphp--}}
                        </td>
                        <td class="text-center">
                            @if(isset($director['endDate']) && $director['endDate'] != null)
                                {{ date("d M Y", strtotime($director['endDate'])) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <span style="font-size: 11px; color: #6c757d">Date Printed: @php echo date("d M Y")  @endphp</span>
        </div>
    </div>

    <!-- SHAREHOLDERS -->
    @if($shareholders && count($shareholders) > 0)
        <div class="container default-font break">
            <x-compliance-header
                :companyName="$company_name"
                :registryNumber="$registry_number"
                :incorporationDate="$incorporation_date"
                :registeredAddress="$registered_address"
                :organizationType="$organization_type"
                :authority="$authority"
                :title="'Register of Members'"
                :countryOfIncorporation="$country_of_incorporation"
                :tin="$tin ?? 'Not Provided'"
                :companyStatusText="$status"
            />
            <div>
                <table class="table" style="margin-top:2rem">
                    <thead>
                    <tr>
                        <th style="width:300px;">Name</th>
                        <th style="width:300px;">Address</th>
                        <th style="width:100px;">Status of Member</th>
                        <th style="width:100px;">Number of Shares</th>
                        <th style="width:100px;">Date Entered</th>
                        <th style="width:100px;">Date Ceased</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($shareholders as $shareholder)
                        <tr>
                            <td>{{ isset($shareholder['name']) ? $shareholder['name']: '' }}</td>
                            <td>{{ isset($shareholder['address']) ? $shareholder['address']: '' }}</td>
                            <td>{{ isset($shareholder['memberType']) ? $shareholder['memberType']: '' }}</td>
                            <td>{{ isset($shareholder['shares']) ? $shareholder['shares']: '' }}</td>
                            <td class="text-center">
                                @if(isset($shareholder['startDate']) && $shareholder['startDate'] != null)
                                    {{ date("d M Y", strtotime($shareholder['startDate'])) }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($shareholder['endDate']) && $shareholder['endDate'] != null)
                                    {{ date("d M Y", strtotime($shareholder['endDate'])) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <span style="font-size: 11px; color: #6c757d">Date Printed: @php echo date("d M Y")  @endphp</span>
            </div>
        </div>
    @endif
    <!-- UBO -->

    @if($ubos && count($ubos) > 0)
        <div class="container default-font">
            <x-compliance-header
                :companyName="$company_name"
                :registryNumber="$registry_number"
                :incorporationDate="$incorporation_date"
                :registeredAddress="$registered_address"
                :organizationType="$organization_type"
                :authority="$authority"
                :title="'Register of Beneficial Owners'"
                :countryOfIncorporation="$country_of_incorporation"
                :tin="$tin ?? 'Not Provided'"
                :companyStatusText="$status"
            />
            <div>
                <table class="table" style="margin-top:2rem">
                    <thead>
                    <tr>
                        <th style="width:300px;" rowspan="2">Name and Residential Address of Beneficial Owner</th>
                        <!-- <th style="width:300px;">Address</th> -->
                        <th style="width:100px;" rowspan="2">Date of Birth</th>
                        <th style="width:100px;" rowspan="2">Nationality</th>
                        <th style="width:100px;" rowspan="2">Passport No / National Identification Number</th>
                        <th style="width:100px;" rowspan="2">TIN</th>
                        <th style="width:100px;" rowspan="2">Nature of Interest Held</th>
                        <th style="width:100px;" colspan="4">Details of each member (if nominee) holding shares for and
                            on behalf of beneficial owner
                        </th>
                        <th style="width:100px;" rowspan="2">Date became the beneficial owner</th>
                        <th style="width:100px;" rowspan="2">Date ceased to be the beneficial owner</th>
                    </tr>
                    <tr>
                        <th style="width:200px;">Name, address, date of birth and nationality of each nominee</th>
                        <th>Particulars and details of the interest held by nominee</th>
                        <th>Name, residential address, date of birth and nationality of each nominator</th>
                        <th>Date became nominee</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ubos as $ubo)
                        <tr>
                            <td>
                                <div>{{isset($ubo['name']) ? $ubo['name']: '' }}</div>
                                <div class="address">{{isset($ubo['address']) ? $ubo['address']: '' }}</div>
                            </td>
                            <td>{{isset($ubo['dateOfBirth']) ? $ubo['dateOfBirth']: '' }}</td>
                            <td>{{isset($ubo['nationality']) ? $ubo['nationality']: '' }}</td>
                            <td>{{isset($ubo['passportNumber']) ? $ubo['passportNumber']: '' }}</td>
                            <td>{{isset($ubo['tin']) ? $ubo['tin']: '' }}</td>
                            <td>{{isset($ubo['shares']) ? $ubo['shares']: ''  }}</td>
                            @if(count($ubo['nomineeList']) > 0)
                                @foreach($ubo['nomineeList'] as $nominee)
                                    <td>
                                        <div class="nominee">{{isset($nominee['name']) ? $nominee['name']: '' }}</div>
                                        <div
                                            class="address">{{isset($nominee['address']) ? $nominee['address']: '' }}</div>
                                    </td>
                                    <td class="nominee">{{isset($nominee['shares']) ? $nominee['shares']: ''  }}</td>
                                    <td class="nominee">
                                        @if(isset($nominee['nominator']) && $nominee['nominator']['typeId'] == 3)
                                            <div
                                                class="nominee">{{isset($nominee['nominator']['name']) ? $nominee['nominator']['name']: '' }}</div>
                                            <div
                                                class="address">{{isset($nominee['nominator']['address']) ? $nominee['nominator']['address']: '' }}</div>
                                            <div
                                                class="address">{{isset($nominee['nominator']['dateOfBirth']) ? $nominee['nominator']['dateOfBirth']: '' }}</div>
                                            <div
                                                class="address">{{isset($nominee['nominator']['nationality']) ? $nominee['nominator']['nationality']: '' }}</div>

                                        @elseif(isset($nominee['nominator']) && $nominee['nominator']['typeId'] == 4)
                                            <div
                                                class="nominee">{{isset($nominee['nominator']['name']) ? $nominee['nominator']['name']: '' }}</div>
                                            <div
                                                class="address">{{isset($nominee['nominator']['address']) ? $nominee['nominator']['address']: '' }}</div>
                                        @endif

                                    </td>
                                    <td class="nominee">
                                        @if(isset($nominee['startDate']) && $nominee['startDate'] != null)
                                            {{ date("d M Y", strtotime($nominee['startDate'])) }}
                                        @endif
                                    </td>
                                    {{--                                        <td class="nominee">--}}
                                    {{--                                            @php--}}
                                    {{--                                            if($nominee['endDate'] != null)--}}
                                    {{--                                                echo date("d M Y", strtotime($nominee['endDate'] ));--}}
                                    {{--                                            @endphp--}}
                                    {{--                                        </td>--}}
                                @endforeach

                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                            <td class="text-center">
                                @if(isset($ubo['startDate']) && $ubo['startDate'] != null)
                                    {{ date("d M Y", strtotime($ubo['startDate'])) }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if(isset($ubo['endDate']) && $ubo['endDate'] != null)
                                    {{ date("d M Y", strtotime($ubo['endDate'])) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <span style="font-size: 11px; color: #6c757d">Date Printed: @php echo date("d M Y")  @endphp</span>
            </div>
        </div>
    @endif

</main>
</body>

</html>
