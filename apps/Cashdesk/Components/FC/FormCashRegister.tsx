import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCashRegisterProps extends FormProps {}

const componentName = 'FormCashRegister'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Cashdesk';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormCashRegisterProps) => {
  return <>
    <Input field='id_company' />
    <Input field='id_shop' />
    <Input field='identifier' />
    <Input field='description' />
  </>;
}

/** FormCashRegister */
const FormCashRegister = (props: FormCashRegisterProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CashRegister'}
    urlSlug='cashdesk/cash-registers'
    title={{field: 'identifier', sub: T.translate('Cash register')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormCashRegister;
