import React, { Component } from 'react';
import FormActivity from '@hubleto/apps/Calendar/Components/FC/FormActivity'
import FormInput from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Lookup from '@hubleto/react-ui/components/cc/Inputs/Lookup';
import Translator from "@hubleto/react-ui/core/Translator";
import { FormActivityProps } from '@hubleto/apps/Calendar/Components/FC/FormActivity';
import { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField, FormRecordStoreContext, getRecord } from '@hubleto/react-ui/components/fc/FormRecordStore';
import ModalForm from '@hubleto/react-ui/components/cc/ModalForm';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';

export interface LeadFormActivityProps extends FormProps {
  form?: any,
  id?: number,
  idCustomer?: number,
  idContact?: number,
  idLead?: number,
  showIdActivity?: number,
}

const LeadFormActivityCustomInputs = (props: LeadFormActivityProps) => {
  const { form, id, idCustomer, idContact, idLead } = props;

  const translate = new Translator(
    'HubletoApp\\Community\\Leads\\Loader',
    'Components\\LeadFormActivity'
  ).translate;

  return <>
    <FormInput name='id_lead' customInputProperties={{ readonly: id > 0 }}></FormInput>
    <FormInput title={translate("Contact")}>
      <Lookup {...form.getInputProps('id_contact')}
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
    </FormInput>
  </>;
};

const LeadFormActivity = (props: LeadFormActivityProps) => {
  const { form, id, idCustomer, idContact, idLead } = props;

  return <FormActivity
    id={id}
    model='Hubleto/App/Community/Leads/Models/LeadActivity'
    activitySource='Lead'
    renderCustomInputs={(form: typeof FormMetaContext): React.JSX.Element => {
      return <LeadFormActivityCustomInputs
        form={form}
        id={id}
        idCustomer={idCustomer}
        idContact={idContact}
        idLead={idLead}
      />;
    }}
    {...props}
  ></FormActivity>;
}

export default LeadFormActivity;