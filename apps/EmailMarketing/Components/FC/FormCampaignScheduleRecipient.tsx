import React, { Component } from 'react';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCampaignScheduleRecipientProps extends FormProps {}

const componentName = 'FormCampaignScheduleRecipient';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormCampaignScheduleRecipient');

/** TabDefault */
const TabDefault = (props: FormCampaignScheduleRecipientProps) => {
  return <>
    <Input field='id_campaign_schedule' />
    <Input field='id_recipient' />
    <Input field='id_email' />
  </>;
}

/** FormCampaignScheduleRecipient */
const FormCampaignScheduleRecipient = (props: FormCampaignScheduleRecipientProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CampaignSchedule'}
    urlSlug='email-marketing/campaign/schedules/recipients'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    title={{field: 'title', sub: <>{T.translate('Campaign')} » {T.translate('Scheduled email recipient')}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormCampaignScheduleRecipient;
