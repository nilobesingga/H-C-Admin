<!DOCTYPE html>
<meta charset="UTF-8">

<html>
<head>
<!-- <link rel="stylesheet" href="{{public_path('css/bootstrap-print.min.css')}}" media="all" /> -->
<!-- <link rel="stylesheet" href="{{public_path('css/print-invoice.css')}}" media="all" />  -->
</head>

<style>
body{
    font-family: Nunito,sans-serif;
    font-size: .9rem;
    font-weight: 400;
    line-height: 1.6;
    color: #212529;
}

.orange{
    color: #c63;
}

.jurisdiction-header{
    font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
    font-size: 20px;
    color: white ;
    background-color: #cc6533 ;
    padding: 10px 10px 6px 10px;
    font-weight: bold;
}


.logo{
    float: right
}

.text-center{
    text-align: center;
}

.float-right{
    float: right;
}

.float-left{
    float: left;
}

.label{
    font-size: 13px;
    height: 20px;
    color: #6c757d!important;
}

.header-labels {
    color: #c63;
    font-weight: bold;
    font-size: 20px;
    padding: 0px !important;
}

.table{
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
    display: table;
    text-indent: initial;
    border-spacing: 2px;
    border-color: grey;
    border-top: 3px solid #cc6634;
    /*page-break-inside: avoid;*/
}

thead{
    display: table-header-group;
    vertical-align: middle;
    border-color: inherit;
}

tr{
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
}

.table thead th {
    font-size: 10px;
    border: 1px solid;
    vertical-align: middle;
    padding: 0.75rem;
}

tbody{
    display: table-row-group;
    vertical-align: middle;
    border-color: inherit;
}




td{
    border: 1px solid;
    padding: 0.5em;
    white-space: wrap;
    overflow: visible;
}

.break{
    page-break-after: always;
    page-break-inside: avoid;
}

.nominee{
    font-size: 12px;
}

.address{
   font-size:11px;
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

    <div  class="container default-font" >
            <x-compliance_header
                :companyName="$company_name"
                :registryNumber="$registry_number"
                :incorporationDate="$incorporation_date"
                :registeredAddress="$registered_address"
                :organizationType="$organization_type"
                :authority="$authority"
                :title="'Register of Founders'"
                :countryOfIncorporation="$country_of_incorporation"
                :tin="$tin ?? 'Not Provided'"
                :companyStatusText="$status"
            />


            <div>

                <table class="table" style="margin-top:2rem">
                    <thead>
                        <tr>
                            <th style="width:100px;">Date of Appointment</th>
                            <th style="width:300px;">Full Name</th>
                            <th style="width:100px;">Nationality & Date of Birth</th>
                            <th style="width:300px;">Address</th>
                            <th style="width:100px;">Business Occupation</th>
                            <th style="width:100px;">Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($founders as $founder)
                            <tr>
                                <td class="text-center"> @php echo date("d M Y", strtotime($founder['startDate'] )); @endphp</td>
                                <td>{{isset($founder['name']) ? $founder['name']: '' }}</td>
                                <td>{{isset($founder['dateOfBirth']) ? $founder['dateOfBirth'] : ''}} <br>
                                    {{isset($founder['nationality']) ? $founder['nationality']: ''}}
                                </td>
                                <td>{{isset($founder['address']) ? $founder['address']: '' }}</td>
                                <td>{{isset($founder['role']) ? $founder['role']: '' }}</td>
                                <td>{{isset($founder['notes']) ? $founder['notes']: '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>

            <div>
            <span style="font-size: 11px; color: #6c757d">Date Printed: @php echo date("d M Y")  @endphp</span>
            </div>

    </div>


    </main>
</body>

</html>
