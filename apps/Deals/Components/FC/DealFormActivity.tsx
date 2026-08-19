import React, { Component } from 'react';
import FormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarTabFormActivity'
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Lookup from '@hubleto/react-ui/components/cc/Inputs/Lookup';
import Translator from "@hubleto/react-ui/core/Translator";
import { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';

export interface DealFormActivityProps extends FormProps {
  form?: any,
  id?: number,
  idCustomer?: number,
  idContact?: number,
  idDeal?: number,
  showIdActivity?: number,
}

const T = new Translator(
  'HubletoApp\\Community\\Deals\\Loader',
  'Components\\DealFormActivity'
);

const DealFormActivityCustomInputs = (props: DealFormActivityProps) => {
  const { form, id, idCustomer, idContact, idDeal } = props;

  return <>
    <Input field='id_deal' customInputProps={{ readonly: id > 0 }}></Input>
    <Input field='id_contact' title={T.translate("Contact")}>
      <Lookup
        model='Hubleto/App/Community/Contacts/Models/Contact'
        endpoint={`contacts/api/get-customer-contacts`}
        customEndpointParams={{ id_customer: idCustomer }}
        value={idContact}
        onChange={(input: any, value: any) => {
          form.changeRecord({ id_contact: value })
          // if (R.id_contact == 0) {
          //   R.id_contact = null;
          //   form.setState({record: R})
          // }
        }}
      ></Lookup>
    </Input>
  </>;
};

const DealFormActivity = (props: DealFormActivityProps) => {
  const { form, id, idCustomer, idContact, idDeal } = props;

  return <FormActivity
    id={id}
    model='Hubleto/App/Community/Deals/Models/DealActivity'
    activitySource='Deal'
    renderCustomInputs={(form: typeof FormMetaContext): React.JSX.Element => {
      return <DealFormActivityCustomInputs
        form={form}
        id={id}
        idCustomer={idCustomer}
        idContact={idContact}
        idDeal={idDeal}
      />;
    }}
    {...props}
  ></FormActivity>;
}

export default DealFormActivity;