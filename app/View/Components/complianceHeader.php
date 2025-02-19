<?php

namespace App\View\Components;

use Illuminate\View\Component;

class complianceHeader extends Component
{
    public $companyName;
    public $registryNumber;
    public $incorporationDate;
    public $registeredAddress;
    public $organizationType;
    public $authority;
    public $title;
    public $countryOfIncorporation;
    public $tin;
    public $companyStatusText;
    /**
     * Create a new component instance.
     *
     * @return void
     */



    public function __construct($companyName, $registryNumber, $incorporationDate, $registeredAddress, $organizationType, $authority, $title, $countryOfIncorporation, $tin, $companyStatusText)
    {
        $this->companyName = $companyName;
        $this->registryNumber = $registryNumber;
        $this->incorporationDate = $incorporationDate;
        $this->registeredAddress = $registeredAddress;
        $this->organizationType = $organizationType;
        $this->authority = $authority;
        $this->title = $title;
        $this->countryOfIncorporation = $countryOfIncorporation;
        $this->tin = $tin;
        $this->companyStatusText = $companyStatusText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('compliance.compliance_header');
    }
}
