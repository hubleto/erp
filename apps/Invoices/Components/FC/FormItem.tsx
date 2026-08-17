import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormItemProps extends FormProps {}

const componentName = 'FormItem'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormItemProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='id_invoice' />
    <Input field='date_item' />
    <Input field='amount' />
    <Input field='is_advance_item' />
  </>;
}

/** FormItem */
const FormItem = (props: FormItemProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Item'}
    urlSlug='invoices/items'
    title={{fields: ['date', 'amount'], sub: T.translate('Item')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormItem;
