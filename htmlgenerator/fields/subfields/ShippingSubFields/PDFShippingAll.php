<?php
namespace rnwcinv\htmlgenerator\fields\subfields\ShippingSubFields;
use rnwcinv\htmlgenerator\fields\subfields\PDFSubFieldBase;

class PDFShippingAll extends PDFSubFieldBase {
    public function FormatValue($value,$format='')
    {
        $content='';


        if($this->GetBooleanValue('showCustomerName',true))
            $this->ProcessField('CustomerName',$content,$format);

        if($this->GetBooleanValue('showCompanyName',true))
            $this->ProcessField('Company',$content,$format);

        if($this->GetBooleanValue('showEmail',true))
            $this->ProcessField('Email',$content,$format);

        if($this->GetBooleanValue('showAddress',true))
        {
            $this->ProcessField('Address1', $content,$format);
            $this->ProcessField('Address2', $content,$format);
        }

        $this->CreateCityStateZipRow($content,$format);

        if($this->GetBooleanValue('showCountry',true))
            $this->ProcessField('Country',$content,$format);


        return $content;
    }

    private function ProcessField($type, &$table,$format)
    {
        if($this->orderValueRetriever->useTestData)
            $value=$this->GetTestValue($type);
        else
            $value=$this->GetValue($type);
        if($format=='plain')
        {
            if(trim($value)!='')
            {
                if($table!='')
                    $table.=', ';
            }

            $table.=$value;
        }else
            $table.=$this->CreateRow($type,$value);    }

    private function CreateRow($type,$rowValue)
    {
        $textAlign='';
        if($this->GetStringValue('labelPosition')=='left')
            $textAlign='text-align:right;';
        $rowValue=str_replace('<br/>','',$rowValue);
        if($type=='Address1'||$type=='Address2')
        {
            $type.=' field_Address';
        }
        return '<p class="field_'.$type.'" style="margin:0;padding:0;'.$textAlign.'">'.htmlspecialchars($rowValue).'</p>';
    }

    private function GetValue($type)
    {
        switch ($type)
        {
            case 'CustomerName':
                return $this->orderValueRetriever->get('shipping_first_name').' '. $this->orderValueRetriever->get('shipping_last_name');
            case 'Email':
                return $this->orderValueRetriever->get('shipping_email');
            case 'Phone':
                return $this->orderValueRetriever->get('shipping_phone');
            case 'Company':
                return $this->orderValueRetriever->get('shipping_company');
            case 'Address':
                $address=$this->orderValueRetriever->get('shipping_address_1');
                $address2=$this->orderValueRetriever->get('shipping_address_2');

                if(strlen(trim($address2))>0)
                    $address.="\n ".$address2;
                return $address;
            case 'Address1':
                return $this->orderValueRetriever->get('shipping_address_1');
            case 'Address2':
                return $this->orderValueRetriever->get('shipping_address_2');

            case 'Country':
                $country= $this->orderValueRetriever->get('shipping_country');
                if($this->GetBooleanValue('showFullName',false)&&isset(WC()->countries->countries[$country]))
                    $country=WC()->countries->countries[$country];

                return $country;

            case 'City':
                return $this->orderValueRetriever->get('shipping_city');
            case 'State':
                $country= $this->orderValueRetriever->get('shipping_country');
                $state= $this->orderValueRetriever->get('shipping_state');
                if($this->GetBooleanValue('showStateFullName',false)&&isset(WC()->countries->get_states($country)[$state]))
                    $state=WC()->countries->get_states($country)[$state];

                return \html_entity_decode($state);
            case 'Zip':
                return $this->orderValueRetriever->get('shipping_postcode');

        }
        return '';
    }

    private function GetTestValue($type)
    {
        switch ($type)
        {
            case 'CustomerName':
                return 'Customer Name';
            case 'Email':
                return 'Customer@email.com';
            case 'Company':
                return 'Awesome Company';
            case 'Address':
                return 'Address goes here #323';
            case 'Country':
                return 'Country';
            case 'City':
                return 'City';
            case 'State':
                return 'State';
            case 'Zip':
                return 'Zip';

        }
        return '';
    }

    private function CreateCityStateZipRow(&$table,$format)
    {
        if($this->orderValueRetriever->useTestData)
        {
            $city=$this->GetTestValue('City');
            $state=$this->GetTestValue('State');
            $zip=$this->GetTestValue('Zip');
        }else
        {
            $city=$this->GetValue('City');
            $state=$this->GetValue('State');
            $zip=$this->GetValue('Zip');
        }

        $value='';

        if($this->GetBooleanValue('showCity'))
            $value=$city;
        if($this->GetBooleanValue('showState'))
        {
            if (strlen($value) > 0&&strlen($state)>0)
                $value.=", ";
            $value .= $state;
        }

        if($this->GetBooleanValue('showZip'))
        {
            if (strlen($value) > 0&&strlen($zip)>0)
                $value.=", ";
            $value .= $zip;
        }

        if($format=='plain')
        {
            if(trim($value)!='')
            {
                if($table!='')
                    $table.=', ';
            }

            $table.=$value;
        }else
            $table.=$this->CreateRow('CityStateZip',$value);

    }

    public function GetTestFieldValue()
    {
        // TODO: Implement GetTestFieldValue() method.
    }

    public function GetWCFieldName()
    {
        // TODO: Implement GetWCFieldName() method.
    }
}