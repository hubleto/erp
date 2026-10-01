import React from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableMessages from './TableMessages';

export interface FormCampaignProps extends FormProps {}

const componentName = 'FormCampaign'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormCampaignProps) => {
  const isClosed: boolean = useRecordField('is_closed', false);
  return <div className='flex flex-col gap-2 md:flex-row'>
    <div className='flex-1'>
      <Input field='name' customInputProps={{cssClass: 'text-2xl', readonly: isClosed}} />
      <Input field='target' customInputProps={{readonly: isClosed}} />
      <Input field='goal' customInputProps={{readonly: isClosed}} />
    </div>
    <div className='flex-1'>
      <Input field='id_owner' customInputProps={{readonly: isClosed}} />
      <Input field='is_closed' />
      <Input field='date_created' />
    </div>
  </div>;
}

/** TabMessages */
const TabMessages = (props: FormCampaignProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the campaign first')}</div>;
  return <TableMessages
    tag={"table_campaign_messages"}
    parentForm={form}
    uid={form.uid + "_table_campaign_messages"}
    idCampaign={form.id}
  />;
}


/** FormCampaign */
const FormCampaign = (props: FormCampaignProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Campaign'}
    urlSlug='linkedin-messages/campaigns'
    tabs={{
      default: { title: <b>{T.translate('Campaign')}</b>, content: () => <TabDefault {...props} /> },
      messages: { title: T.translate('Messages'), content: () => <TabMessages {...props} /> },
    }}
    title={{fields: ['name'], sub: T.translate('LinkedIn campaign')}}
    {...props}
  ></Form>;
}

export default FormCampaign;
