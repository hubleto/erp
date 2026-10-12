import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormCurrencyRate'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/Settings';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormCurrencyRate */
const FormCurrencyRate = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CurrencyRate'}
    urlSlug='settings/currency-rates'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('Currency rates')}}
    {...props}
  ></Form>;
}

export default FormCurrencyRate;
