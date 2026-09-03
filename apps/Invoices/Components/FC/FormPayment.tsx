import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormPaymentProps extends FormProps {}

const componentName = 'FormPayment'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormPaymentProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='id_invoice' />
    <Input field='date_payment' />
    <Input field='amount' />
    <Input field='is_advance_payment' />
  </>;
}

/** FormPayment */
const FormPayment = (props: FormPaymentProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Payment'}
    urlSlug='invoices/payments'
    title={{fields: ['date', 'amount'], sub: T.translate('Payment')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormPayment;
