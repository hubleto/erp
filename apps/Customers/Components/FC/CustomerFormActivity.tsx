import React, { Component } from 'react';
import CalendarFormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarFormActivity'
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';

export interface CustomerFormActivityProps extends FormProps {
  form?: any,
  id?: number,
  idCustomer?: number,
  showIdActivity?: number,
}

const CustomerFormActivity = (props: CustomerFormActivityProps) => {
  const { id } = props;

  return <CalendarFormActivity
    id={id}
    model='Hubleto/App/Community/Customers/Models/CustomerActivity'
    activitySource='Customer'
    renderCustomInputs={(form: typeof FormMetaContext): React.JSX.Element => {
      return <>
        <Input field='id_customer' customInputProps={{ readonly: props.id > 0 }}></Input>
      </>;
    }}
    {...props}
  ></CalendarFormActivity>;
}

export default CustomerFormActivity;