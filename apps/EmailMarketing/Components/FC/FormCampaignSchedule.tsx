import React, { Component } from 'react';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCampaignScheduleProps extends FormProps {}

const componentName = 'FormCampaignSchedule';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormCampaignSchedule');

/** TabDefault */
const TabDefault = (props: FormCampaignScheduleProps) => {
  const EMAIL: any = useRecordField('EMAIL');
  const day: number = useRecordField('day');

  return <div className='flex flex-col h-full'>
    <div className='flex gap-2'>
      <div><Input field='day' /></div>
      <div className='grow'><Input field='id_email' wrapperCssClass='flex gap-2' /></div>
    </div>
    <div className='mt-8'>
      {EMAIL ? <div className='card'>
        <div className='card-header'>From: {EMAIL.SENDER_ACCOUNT?.name}</div>
        <div className='card-header'>Subject: {EMAIL.mail_subject}</div>
        <div className='card-body'>
          <iframe
            src="about:blank"
            className='w-full min-h-96'
            srcDoc={EMAIL.mail_body}
          />
        </div>
      </div>
      : <div className='alert alert-warning'>Select email to be sent on <b>day {day}</b></div>}
    </div>
  </div>;
}

/** FormCampaignSchedule */
const FormCampaignSchedule = (props: FormCampaignScheduleProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CampaignSchedule'}
    urlSlug='email-marketing/campaign/schedules'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    title={{main: <>{T.translate('Campaign')} » {T.translate('Scheduled email')}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormCampaignSchedule;
