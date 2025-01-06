<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitrix_lists_sage_companies_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('bitrix_list_id');
            $table->string('sage_company_code')->nullable();
            $table->string('bitrix_sage_company_id')->nullable();
            $table->string('bitrix_sage_company_name')->nullable();
            $table->string('bitrix_category_id')->nullable();
            $table->string('bitrix_category_name')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('bitrix_list_id')->references('id')->on('bitrix_lists')->onDelete('cascade');
        });

        DB::table('bitrix_lists_sage_companies_mapping')->insert([
            // Purchase Invoices
            ["category_id" => 1, "bitrix_list_id" => 1, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1603", "bitrix_category_name" => "Abode Options (G)"],

            ["category_id" => 2, "bitrix_list_id" => 1, "sage_company_code" => "BHGINC", "bitrix_sage_company_id" => "1679", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Inc. (BHGINC)", "bitrix_category_id" => "1604", "bitrix_category_name" => "Bawbawon (U)"],
            ["category_id" => 2, "bitrix_list_id" => 1, "sage_company_code" => "BGHLTD", "bitrix_sage_company_id" => "1964", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Ltd. (BGHLTD)", "bitrix_category_id" => "1604", "bitrix_category_name" => "Bawbawon (U)"],

            ["category_id" => 3, "bitrix_list_id" => 1, "sage_company_code" => "BINSEZ", "bitrix_sage_company_id" => "1925", "bitrix_sage_company_name" => "BINCRES Trading Limited. (BINSEZ)", "bitrix_category_id" => "1605", "bitrix_category_name" => "Bincres (B)"],

            ["category_id" => 4, "bitrix_list_id" => 1, "sage_company_code" => "CLPUSD", "bitrix_sage_company_id" => "1622", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Seychelles) (CLPUSD)", "bitrix_category_id" => "1606", "bitrix_category_name" => "Cap Lion Point (I)"],
            ["category_id" => 4, "bitrix_list_id" => 1, "sage_company_code" => "MZHLTD", "bitrix_sage_company_id" => "2220", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Cyprus) (MZHLTD)", "bitrix_category_id" => "1606", "bitrix_category_name" => "Cap Lion Point (I)"],

            ["category_id" => 5, "bitrix_list_id" => 1, "sage_company_code" => "ACCDXB", "bitrix_sage_company_id" => "1623", "bitrix_sage_company_name" => "CRESCO Accounting Dubai (ACCDXB)", "bitrix_category_id" => "1607", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 1, "sage_company_code" => "ACCSEZ", "bitrix_sage_company_id" => "1624", "bitrix_sage_company_name" => "CRESCO Accounting Seychelles (ACCSEZ)", "bitrix_category_id" => "1607", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 1, "sage_company_code" => "CRESHD", "bitrix_sage_company_id" => "1630", "bitrix_sage_company_name" => "CRESCO Shared Services Inc. (CRESHD)", "bitrix_category_id" => "1607", "bitrix_category_name" => "CRESCO_Accounting (N)"],

            ["category_id" => 6, "bitrix_list_id" => 1, "sage_company_code" => "CMPLTD", "bitrix_sage_company_id" => "1942", "bitrix_sage_company_name" => "CRESCO Compliance Limited (CMPLTD)", "bitrix_category_id" => "1608", "bitrix_category_name" => "CRESCO_Compliance (O)"],

            ["category_id" => 7, "bitrix_list_id" => 1, "sage_company_code" => "CRESCO", "bitrix_sage_company_id" => "1660", "bitrix_sage_company_name" => "CRESCO Holding (CRESCO)", "bitrix_category_id" => "1609", "bitrix_category_name" => "CRESCO_Holding (Z)"],
            ["category_id" => 7, "bitrix_list_id" => 1, "sage_company_code" => "CMCLLC", "bitrix_sage_company_id" => "1876", "bitrix_sage_company_name" => "CRESCO Management Consultancy LLC (CMCLLC)", "bitrix_category_id" => "1609", "bitrix_category_name" => "CRESCO_Holding (Z)"],

            ["category_id" => 8, "bitrix_list_id" => 1, "sage_company_code" => "LGLDXB", "bitrix_sage_company_id" => "1628", "bitrix_sage_company_name" => "CRESCO Legal Dubai (LGLDXB)", "bitrix_category_id" => "1610", "bitrix_category_name" => "CRESCO_Legal (C)"],

            ["category_id" => 9, "bitrix_list_id" => 1, "sage_company_code" => "PWRSEZ", "bitrix_sage_company_id" => "1940", "bitrix_sage_company_name" => "CRESCO Power Limited (PWRSEZ)", "bitrix_category_id" => "1611", "bitrix_category_name" => "CRESCO_Power (K)"],
            ["category_id" => 9, "bitrix_list_id" => 1, "sage_company_code" => "POWER", "bitrix_sage_company_id" => "1685", "bitrix_sage_company_name" => "CRESCO Power Philippines Inc. (POWER)", "bitrix_category_id" => "1611", "bitrix_category_name" => "CRESCO_Power (K)"],

            ["category_id" => 10, "bitrix_list_id" => 1, "sage_company_code" => "TECDXB", "bitrix_sage_company_id" => "1631", "bitrix_sage_company_name" => "CRESCOTEC LLC FZ (TECDXB)", "bitrix_category_id" => "1612", "bitrix_category_name" => "CRESCO_tec (F)"],

            ["category_id" => 11, "bitrix_list_id" => 1, "sage_company_code" => "HCDXB", "bitrix_sage_company_id" => "1625", "bitrix_sage_company_name" => "Hensley&Cook Management Services (HCDXB)", "bitrix_category_id" => "1613", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 1, "sage_company_code" => "HCSGPR", "bitrix_sage_company_id" => "1627", "bitrix_sage_company_name" => "Hensley&Cook Pte. Ltd. (HCSGPR)", "bitrix_category_id" => "1613", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 1, "sage_company_code" => "HCSEZ", "bitrix_sage_company_id" => "1626", "bitrix_sage_company_name" => "Hensley&Cook Seychelles (HCSEZ)", "bitrix_category_id" => "1613", "bitrix_category_name" => "HensleyCook (T)"],

            ["category_id" => 12, "bitrix_list_id" => 1, "sage_company_code" => "OFRLLC", "bitrix_sage_company_id" => "1832", "bitrix_sage_company_name" => "Lionsrock (L) (OFRLLC)", "bitrix_category_id" => "1831", "bitrix_category_name" => "Lionsrock (L)"],

            ["category_id" => 13, "bitrix_list_id" => 1, "sage_company_code" => "SADIQA", "bitrix_sage_company_id" => "1633", "bitrix_sage_company_name" => "Sadiqa LLC (SADIQA)", "bitrix_category_id" => "1614", "bitrix_category_name" => "Sadiqa (Q)"],
            ["category_id" => 13, "bitrix_list_id" => 1, "sage_company_code" => "SADSEZ", "bitrix_sage_company_id" => "1945", "bitrix_sage_company_name" => "Sadiqa Limited (SADSEZ)", "bitrix_category_id" => "1614", "bitrix_category_name" => "Sadiqa (Q)"],

            ["category_id" => 14, "bitrix_list_id" => 1, "sage_company_code" => "SMDXB", "bitrix_sage_company_id" => "1632", "bitrix_sage_company_name" => "Smart Money (SMDXB)", "bitrix_category_id" => "1615", "bitrix_category_name" => "SmartMoney (Y)"],

            ["category_id" => 15, "bitrix_list_id" => 1, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1872", "bitrix_company_name" => "Orchidx"],

            // Cash Requisition
            ["category_id" => 1, "bitrix_list_id" => 2, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1636", "bitrix_category_name" => null],

            ["category_id" => 2, "bitrix_list_id" => 2, "sage_company_code" => "BHGINC", "bitrix_sage_company_id" => "1680", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Inc. (BHGINC)", "bitrix_category_id" => "1637", "bitrix_category_name" => "Bawbawon (U)"],

            ["category_id" => 3, "bitrix_list_id" => 2, "sage_company_code" => "BINSEZ", "bitrix_sage_company_id" => "1923", "bitrix_sage_company_name" => "BINCRES Trading Limited. (BINSEZ)", "bitrix_category_id" => "1638", "bitrix_category_name" => "Bincres (B)"],

            ["category_id" => 4, "bitrix_list_id" => 2, "sage_company_code" => "CLPUSD", "bitrix_sage_company_id" => "1664", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Seychelles) (CLPUSD)", "bitrix_category_id" => "1639", "bitrix_category_name" => "Cap Lion Point (I)"],
            ["category_id" => 4, "bitrix_list_id" => 2, "sage_company_code" => "MZHLTD", "bitrix_sage_company_id" => "2266", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Cyprus) (MZHLTD)", "bitrix_category_id" => "1639", "bitrix_category_name" => "Cap Lion Point (I)"],

            ["category_id" => 5, "bitrix_list_id" => 2, "sage_company_code" => "ACCDXB", "bitrix_sage_company_id" => "1665", "bitrix_sage_company_name" => "CRESCO Accounting Dubai (ACCDXB)", "bitrix_category_id" => "1640", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 2, "sage_company_code" => "ACCSEZ", "bitrix_sage_company_id" => "1666", "bitrix_sage_company_name" => "CRESCO Accounting Seychelles (ACCSEZ)", "bitrix_category_id" => "1640", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 2, "sage_company_code" => "CRESHD", "bitrix_sage_company_id" => "1672", "bitrix_sage_company_name" => "CRESCO Shared Services Inc. (CRESHD)", "bitrix_category_id" => "1640", "bitrix_category_name" => "CRESCO_Accounting (N)"],

            ["category_id" => 6, "bitrix_list_id" => 2, "sage_company_code" => "CMPLTD", "bitrix_sage_company_id" => "1874", "bitrix_sage_company_name" => "CRESCO Compliance Limited (CMPLTD)", "bitrix_category_id" => "1641", "bitrix_category_name" => "CRESCO_Compliance (O)"],

            ["category_id" => 7, "bitrix_list_id" => 2, "sage_company_code" => "CRESCO", "bitrix_sage_company_id" => "1677", "bitrix_sage_company_name" => "CRESCO Holding (CRESCO)", "bitrix_category_id" => "1642", "bitrix_category_name" => "CRESCO_Holding (Z)"],
            ["category_id" => 7, "bitrix_list_id" => 2, "sage_company_code" => "CMCLLC", "bitrix_sage_company_id" => "1875", "bitrix_sage_company_name" => "CRESCO Management Consultancy LLC (CMCLLC)", "bitrix_category_id" => "1642", "bitrix_category_name" => "CRESCO_Holding (Z)"],

            ["category_id" => 8, "bitrix_list_id" => 2, "sage_company_code" => "LGLDXB", "bitrix_sage_company_id" => "1670", "bitrix_sage_company_name" => "CRESCO Legal Dubai (LGLDXB)", "bitrix_category_id" => "1643", "bitrix_category_name" => "CRESCO_Legal (C)"],

            ["category_id" => 9, "bitrix_list_id" => 2, "sage_company_code" => "PWRSEZ", "bitrix_sage_company_id" => "1939", "bitrix_sage_company_name" => "CRESCO Power Limited (PWRSEZ)", "bitrix_category_id" => "1644", "bitrix_category_name" => "CRESCO_Power (K)"],
            ["category_id" => 9, "bitrix_list_id" => 2, "sage_company_code" => "POWER", "bitrix_sage_company_id" => "1684", "bitrix_sage_company_name" => "CRESCO Power Philippines Inc. (POWER)", "bitrix_category_id" => "1644", "bitrix_category_name" => "CRESCO_Power (K)"],

            ["category_id" => 10, "bitrix_list_id" => 2, "sage_company_code" => "TECDXB", "bitrix_sage_company_id" => "1673", "bitrix_sage_company_name" => "CRESCOTEC LLC FZ (TECDXB)", "bitrix_category_id" => "1645", "bitrix_category_name" => "CRESCO_tec (F)"],

            ["category_id" => 11, "bitrix_list_id" => 2, "sage_company_code" => "HCDXB", "bitrix_sage_company_id" => "1667", "bitrix_sage_company_name" => "Hensley&Cook Management Services (HCDXB)", "bitrix_category_id" => "1646", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 2, "sage_company_code" => "HCSGPR", "bitrix_sage_company_id" => "1669", "bitrix_sage_company_name" => "Hensley&Cook Pte. Ltd. (HCSGPR)", "bitrix_category_id" => "1646", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 2, "sage_company_code" => "HCSEZ", "bitrix_sage_company_id" => "1668", "bitrix_sage_company_name" => "Hensley&Cook Seychelles (HCSEZ)", "bitrix_category_id" => "1646", "bitrix_category_name" => "HensleyCook (T)"],

            ["category_id" => 12, "bitrix_list_id" => 2, "sage_company_code" => "OFRLLC", "bitrix_sage_company_id" => "1830", "bitrix_sage_company_name" => "Lionsrock (OFRLLC)", "bitrix_category_id" => "1829", "bitrix_category_name" => "Lionsrock (L)"],

            ["category_id" => 13, "bitrix_list_id" => 2, "sage_company_code" => "SADIQA", "bitrix_sage_company_id" => "1675", "bitrix_sage_company_name" => "Sadiqa DMCC (SADIQA)", "bitrix_category_id" => "1647", "bitrix_category_name" => "Sadiqa (Q)"],
            ["category_id" => 13, "bitrix_list_id" => 2, "sage_company_code" => "SADSEZ", "bitrix_sage_company_id" => "1946", "bitrix_sage_company_name" => "Sadiqa Limited (SADSEZ)", "bitrix_category_id" => "1647", "bitrix_category_name" => "Sadiqa (Q)"],

            ["category_id" => 14, "bitrix_list_id" => 2, "sage_company_code" => "SMDXB", "bitrix_sage_company_id" => "1674", "bitrix_sage_company_name" => "Smart Money (SMDXB)", "bitrix_category_id" => "1648", "bitrix_category_name" => "SmartMoney (Y)"],

            // Sale Invoices
            ["category_id" => 1, "bitrix_list_id" => 3, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_Id" => null, "bitrix_category_name" => null],

            ["category_id" => 2, "bitrix_list_id" => 3, "sage_company_code" => "BGHLTD", "bitrix_sage_company_id" => "5287", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Ltd. (BGHLTD)", "bitrix_category_Id" => "1457", "bitrix_category_name" => "Bawbawon (U)"],
            ["category_id" => 2, "bitrix_list_id" => 3, "sage_company_code" => "BGHINC", "bitrix_sage_company_id" => "5288", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Inc. (BGHINC)", "bitrix_category_Id" => "1457", "bitrix_category_name" => "Bawbawon (U)"],

            ["category_id" => 3, "bitrix_list_id" => 3, "sage_company_code" => "BINSEZ", "bitrix_sage_company_id" => "3947", "bitrix_sage_company_name" => "BINCRES Trading Limited. (BINSEZ)", "bitrix_category_Id" => "1452", "bitrix_category_name" => "Bincres (B)"],

            ["category_id" => 4, "bitrix_list_id" => 3, "sage_company_code" => "CLPUSD", "bitrix_sage_company_id" => "3876", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Seychelles) - (CLPUSD)", "bitrix_category_Id" => "868", "bitrix_category_name" => "Cap Lion Point (I)"],

            ["category_id" => 5, "bitrix_list_id" => 3, "sage_company_code" => "ACCDXB", "bitrix_sage_company_id" => "1882", "bitrix_sage_company_name" => "CRESCO Accounting Dubai (ACCDXB)", "bitrix_category_Id" => "865", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 3, "sage_company_code" => "ACCSEZ", "bitrix_sage_company_id" => "1883", "bitrix_sage_company_name" => "CRESCO Accounting Seychelles (ACCSEZ)", "bitrix_category_Id" => "865", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 3, "sage_company_code" => "CRESHD", "bitrix_sage_company_id" => "3882", "bitrix_sage_company_name" => "CRESCO Shared Services Inc. (CRESHD)", "bitrix_category_Id" => "865", "bitrix_category_name" => "CRESCO_Accounting (N)"],

            ["category_id" => 6, "bitrix_list_id" => 3, "sage_company_code" => "CMPLTD", "bitrix_sage_company_id" => "5148", "bitrix_sage_company_name" => "CRESCO Compliance Limited (CMPLTD)", "bitrix_category_Id" => "866", "bitrix_category_name" => "CRESCO_Compliance (O)"],

            ["category_id" => 7, "bitrix_list_id" => 3, "sage_company_code" => "CRESCO", "bitrix_sage_company_id" => "3941", "bitrix_sage_company_name" => "CRESCO Holding (CRESCO)", "bitrix_category_Id" => "867", "bitrix_category_name" => "CRESCO_Holding (Z)"],

            ["category_id" => 8, "bitrix_list_id" => 3, "sage_company_code" => "LGLDXB", "bitrix_sage_company_id" => "2927", "bitrix_sage_company_name" => "CRESCO Legal Dubai (LGLDXB)", "bitrix_category_Id" => "847", "bitrix_category_name" => "CRESCO_Legal (C)"],

            ["category_id" => 9, "bitrix_list_id" => 3, "sage_company_code" => "PWRSEZ", "bitrix_sage_company_id" => "5143", "bitrix_sage_company_name" => "CRESCO Power Limited (PWRSEZ)", "bitrix_category_Id" => "1463", "bitrix_category_name" => "CRESCO_Power (K)"],

            ["category_id" => 10, "bitrix_list_id" => 3, "sage_company_code" => "TECDXB", "bitrix_sage_company_id" => "2977", "bitrix_sage_company_name" => "CRESCOTEC LLC FZ (TECDXB)", "bitrix_category_Id" => "861", "bitrix_category_name" => "CRESCO_tec (F)"],

            ["category_id" => 11, "bitrix_list_id" => 3, "sage_company_code" => "HCDXB", "bitrix_sage_company_id" => "1905", "bitrix_sage_company_name" => "Hensley&Cook Management Services (HCDXB)", "bitrix_category_Id" => "858", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 3, "sage_company_code" => "HCSEZ", "bitrix_sage_company_id" => "1906", "bitrix_sage_company_name" => "Hensley&Cook Seychelles (HCSEZ)", "bitrix_category_Id" => "858", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 3, "sage_company_code" => "HCSGPR", "bitrix_sage_company_id" => "1917", "bitrix_sage_company_name" => "Hensley&Cook Pte. Ltd. (HCSGPR)", "bitrix_category_Id" => "858", "bitrix_category_name" => "HensleyCook (T)"],

            ["category_id" => 12, "bitrix_list_id" => 3, "sage_company_code" => "OFRLLC", "bitrix_sage_company_id" => "3996", "bitrix_sage_company_name" => "Lionsrock (OFRLLC)", "bitrix_category_Id" => "3936", "bitrix_category_name" => "Lionsrock (L)"],

            ["category_id" => 13, "bitrix_list_id" => 3, "sage_company_code" => "SADIQA", "bitrix_sage_company_id" => "3852", "bitrix_sage_company_name" => "Sadiqa LLC (SADIQA)", "bitrix_category_Id" => "856", "bitrix_category_name" => "Sadiqa (Q)"],
            ["category_id" => 13, "bitrix_list_id" => 3, "sage_company_code" => "SADSEZ", "bitrix_sage_company_id" => "5215", "bitrix_sage_company_name" => "Sadiqa Limited (SADSEZ)", "bitrix_category_Id" => "856", "bitrix_category_name" => "Sadiqa (Q)"],

            ["category_id" => 14, "bitrix_list_id" => 3, "sage_company_code" => "SMDXB", "bitrix_sage_company_id" => "3849", "bitrix_sage_company_name" => "Smart Money (SMDXB)", "bitrix_category_Id" => "860", "bitrix_category_name" => "SmartMoney (Y)"],

            // Bank Transfers
            ["category_id" => 1, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1846", "bitrix_company_name" => "Abode Options (G)"],

            ["category_id" => 2, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1847", "bitrix_company_name" => "Bawbawon (U)"],

            ["category_id" => 3, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1848", "bitrix_company_name" => "Bincres (B)"],

            ["category_id" => 4, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1849", "bitrix_company_name" => "Cap Lion Point (I)"],

            ["category_id" => 5, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1850", "bitrix_company_name" => "CRESCO_Accounting (N)"],

            ["category_id" => 6, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1851", "bitrix_company_name" => "CRESCO_Compliance (O)"],

            ["category_id" => 7, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1852", "bitrix_company_name" => "CRESCO_Holding (Z)"],

            ["category_id" => 8, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1853", "bitrix_company_name" => "CRESCO_Legal (C)"],

            ["category_id" => 9, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1854", "bitrix_company_name" => "CRESCO_Power (K)"],

            ["category_id" => 10, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1855", "bitrix_company_name" => "CRESCO_tec (F)"],

            ["category_id" => 11, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1856", "bitrix_company_name" => "HensleyCook (T)"],

            ["category_id" => 12, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1857", "bitrix_company_name" => "Lionsrock (L)"],

            ["category_id" => 13, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1858", "bitrix_company_name" => "Sadiqa (Q)"],

            ["category_id" => 14, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1859", "bitrix_company_name" => "SmartMoney (Y)"],

            ["category_id" => 15, "bitrix_list_id" => 4, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "1873", "bitrix_company_name" => "Orchidx"],

            // Proforma Invoices
            ["category_id" => 1, "bitrix_list_id" => 5, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => "3844", "bitrix_category_name" => "Abode Options (G)"],

            ["category_id" => 2, "bitrix_list_id" => 5, "sage_company_code" => "BHGINC", "bitrix_sage_company_id" => "5284", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Inc. (BHGINC)", "bitrix_category_id" => "1456", "bitrix_category_name" => "Bawbawon (U)"],
            ["category_id" => 2, "bitrix_list_id" => 5, "sage_company_code" => "BGHLTD", "bitrix_sage_company_id" => "5283", "bitrix_sage_company_name" => "Bawbawon Hospitality Group Ltd. (BGHLTD)", "bitrix_category_id" => "1456", "bitrix_category_name" => "Bawbawon (U)"],

            ["category_id" => 3, "bitrix_list_id" => 5, "sage_company_code" => "BINSEZ", "bitrix_sage_company_id" => "3943", "bitrix_sage_company_name" => "BINCRES Trading Limited. (BINSEZ)", "bitrix_category_id" => "1451", "bitrix_category_name" => "Bincres (B)"],

            ["category_id" => 4, "bitrix_list_id" => 5, "sage_company_code" => "CLPUSD", "bitrix_sage_company_id" => "3874", "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Seychelles) (CLPUSD)", "bitrix_category_id" => "766", "bitrix_category_name" => "Cap Lion Point (I)"],
            ["category_id" => 4, "bitrix_list_id" => 5, "sage_company_code" => "MZHLTD", "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => "Cap Lion Point Ltd. (Cyprus) (MZHLTD)", "bitrix_category_id" => "766", "bitrix_category_name" => "Cap Lion Point (I)"],

            ["category_id" => 5, "bitrix_list_id" => 5, "sage_company_code" => "ACCDXB", "bitrix_sage_company_id" => "1877", "bitrix_sage_company_name" => "CRESCO Accounting Dubai (ACCDXB)", "bitrix_category_id" => "763", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 5, "sage_company_code" => "ACCSEZ", "bitrix_sage_company_id" => "1878", "bitrix_sage_company_name" => "CRESCO Accounting Seychelles (ACCSEZ)", "bitrix_category_id" => "763", "bitrix_category_name" => "CRESCO_Accounting (N)"],
            ["category_id" => 5, "bitrix_list_id" => 5, "sage_company_code" => "CRESHD", "bitrix_sage_company_id" => "3880", "bitrix_sage_company_name" => "CRESCO Shared Services Inc. (CRESHD)", "bitrix_category_id" => "763", "bitrix_category_name" => "CRESCO_Accounting (N)"],

            ["category_id" => 6, "bitrix_list_id" => 5, "sage_company_code" => "CMPLTD", "bitrix_sage_company_id" => "5146", "bitrix_sage_company_name" => "CRESCO Compliance Limited (CMPLTD)", "bitrix_category_id" => "764", "bitrix_category_name" => "CRESCO_Compliance (O)"],

            ["category_id" => 7, "bitrix_list_id" => 5, "sage_company_code" => "CRESCO", "bitrix_sage_company_id" => "3939", "bitrix_sage_company_name" => "CRESCO Holding (CRESCO)", "bitrix_category_id" => "765", "bitrix_category_name" => "CRESCO_Holding (Z)"],
            ["category_id" => 7, "bitrix_list_id" => 5, "sage_company_code" => "CMCLLC", "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => "CRESCO Management Consultancy LLC (CMCLLC)", "bitrix_category_id" => "765", "bitrix_category_name" => "CRESCO_Holding (Z)"],

            ["category_id" => 8, "bitrix_list_id" => 5, "sage_company_code" => "LGLDXB", "bitrix_sage_company_id" => "2925", "bitrix_sage_company_name" => "CRESCO Legal Dubai (LGLDXB)", "bitrix_category_id" => "745", "bitrix_category_name" => "CRESCO_Legal (C)"],

            ["category_id" => 9, "bitrix_list_id" => 5, "sage_company_code" => "PWRSEZ", "bitrix_sage_company_id" => "5141", "bitrix_sage_company_name" => "CRESCO Power Limited (PWRSEZ)", "bitrix_category_id" => "1462", "bitrix_category_name" => "CRESCO_Power (K)"],
            ["category_id" => 9, "bitrix_list_id" => 5, "sage_company_code" => "POWER", "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => "CRESCO Power Philippines Inc. (POWER)", "bitrix_category_id" => "1462", "bitrix_category_name" => "CRESCO_Power (K)"],

            ["category_id" => 10, "bitrix_list_id" => 5, "sage_company_code" => "TECDXB", "bitrix_sage_company_id" => "2975", "bitrix_sage_company_name" => "CRESCOTEC LLC FZ (TECDXB)", "bitrix_category_id" => "759", "bitrix_category_name" => "CRESCO_tec (F)"],

            ["category_id" => 11, "bitrix_list_id" => 5, "sage_company_code" => "HCDXB", "bitrix_sage_company_id" => "1901", "bitrix_sage_company_name" => "Hensley&Cook Management Services (HCDXB)", "bitrix_category_id" => "756", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 5, "sage_company_code" => "HCSGPR", "bitrix_sage_company_id" => "1915", "bitrix_sage_company_name" => "Hensley&Cook Pte. Ltd. (HCSGPR)", "bitrix_category_id" => "756", "bitrix_category_name" => "HensleyCook (T)"],
            ["category_id" => 11, "bitrix_list_id" => 5, "sage_company_code" => "HCSEZ", "bitrix_sage_company_id" => "1902", "bitrix_sage_company_name" => "Hensley&Cook Seychelles (HCSEZ)", "bitrix_category_id" => "756", "bitrix_category_name" => "HensleyCook (T)"],

            ["category_id" => 12, "bitrix_list_id" => 5, "sage_company_code" => "OFRLLC", "bitrix_sage_company_id" => "3997", "bitrix_sage_company_name" => "Lionsrock (L) (OFRLLC)", "bitrix_category_id" => "3935", "bitrix_category_name" => "Lionsrock (L)"],

            ["category_id" => 13, "bitrix_list_id" => 5, "sage_company_code" => "SADIQA", "bitrix_sage_company_id" => "3850", "bitrix_sage_company_name" => "Sadiqa LLC (SADIQA)", "bitrix_category_id" => "754", "bitrix_category_name" => "Sadiqa (Q)"],
            ["category_id" => 13, "bitrix_list_id" => 5, "sage_company_code" => "SADSEZ", "bitrix_sage_company_id" => "5213", "bitrix_sage_company_name" => "Sadiqa Limited (SADSEZ)", "bitrix_category_id" => "754", "bitrix_category_name" => "Sadiqa (Q)"],

            ["category_id" => 14, "bitrix_list_id" => 5, "sage_company_code" => "SMDXB", "bitrix_sage_company_id" => "3847", "bitrix_sage_company_name" => "Smart Money (SMDXB)", "bitrix_category_id" => "758", "bitrix_category_name" => "SmartMoney (Y)"],

            ["category_id" => 15, "bitrix_list_id" => 5, "sage_company_code" => null, "bitrix_sage_company_id" => null, "bitrix_sage_company_name" => null, "bitrix_category_id" => null, "bitrix_company_name" => null],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitrix_lists_sage_companies_mapping');
    }
};
