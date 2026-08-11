import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import FormEmail from './FormEmail';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCampaignScheduleRecipientProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\EmailMarketing\\Loader',
  'Components\\FormCampaignScheduleRecipient'
).translate;

/**
 * TabDefault
 *
 * @var [type]
 */
const TabDefault = (props: FormCampaignScheduleRecipientProps) => {
  return <>
    <Input field='id_campaign_schedule' />
    <Input field='id_recipient' />
    <Input field='id_email' />
  </>;
}

/**
 * FormCampaignScheduleRecipient
 *
 * @var [type]
 */
const FormCampaignScheduleRecipient = (props: FormCampaignScheduleRecipientProps) => {
  return <Form
   componentName='FormCampaignScheduleRecipient'
    parentApp='Hubleto/App/Community/EmailMarketing'
    model='Hubleto/App/Community/EmailMarketing/Models/CampaignSchedule'
    urlSlug='email-marketing/campaign/schedules/recipients'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    title={{'field': 'title', sub: <>{translate('Campaign')} » {translate('Scheduled email recipient')}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormCampaignScheduleRecipient;
