import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormVatRate'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormVatRate */
const FormVatRate = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/VatRate'}
    urlSlug='settings/vat-rates'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('VAT rates')}}
    {...props}
  ></Form>;
}

export default FormVatRate;
