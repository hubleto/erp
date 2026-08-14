import React, { Component } from 'react';
import FormActivity from '@hubleto/apps/Calendar/Components/FC/FormActivity'
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Lookup from '@hubleto/react-ui/components/cc/Inputs/Lookup';
import Translator from "@hubleto/react-ui/core/Translator";
import { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';

export interface OrderFormActivityProps extends FormProps {
  form?: any,
  id?: number,
  idOrder?: number,
  showIdActivity?: number,
}

const T = new Translator(
  'HubletoApp\\Community\\Orders\\Loader',
  'Components\\OrderFormActivity'
);

const OrderFormActivityCustomInputs = (props: OrderFormActivityProps) => {
  return <>
    <Input field='id_order' customInputProps={{ readonly: props.id > 0 }}></Input>
  </>;
};

const OrderFormActivity = (props: OrderFormActivityProps) => {
  const { form, id, idOrder } = props;

  return <FormActivity
    id={id}
    model='Hubleto/App/Community/Orders/Models/OrderActivity'
    activitySource='Order'
    renderCustomInputs={(form: typeof FormMetaContext): React.JSX.Element => {
      return <OrderFormActivityCustomInputs
        id={id}
      />;
    }}
    {...props}
  ></FormActivity>;
}

export default OrderFormActivity;