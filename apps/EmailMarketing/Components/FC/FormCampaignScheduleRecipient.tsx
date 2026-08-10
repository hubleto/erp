import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import FormEmail from './FormEmail';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMeta, FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCampaignScheduleRecipientProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\EmailMarketing\\Loader',
  'Components\\FormCampaignScheduleRecipient'
).translate;

/**
 * Title
 *
 * @var [type]
 */
const Title = (props: FormCampaignScheduleRecipientProps) => <>
  <small>{translate('Campaign')} » {translate('Scheduled email recipient')}</small>
  <h2>{useRecordField('email') ?? '-'}</h2>
</>;

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
    customEndpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    {...props}

    uiComponents={{
      title: <Title {...props} />,
      tabs: {
        default: { content: () => <TabDefault {...props} /> },
      },
    }}
  ></Form>;
}

export default FormCampaignScheduleRecipient;
