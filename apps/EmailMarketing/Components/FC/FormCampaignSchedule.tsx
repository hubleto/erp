import React, { Component } from 'react';
import { type FormMeta, type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableCampaignsSchedulesRecipients from './TableCampaignsSchedulesRecipients';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import FormEmail from './FormEmail';

export interface FormCampaignScheduleProps extends FormProps {}

const componentName = 'FormCampaignSchedule';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormCampaignSchedule');

/** TabDefault */
const TabDefault = (props: FormCampaignScheduleProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const EMAIL: any = useRecordField('EMAIL');
  const day: number = useRecordField('day');

  const [showFormEmail, setShowFormEmail] = React.useState(false);

  return <div className='flex-dyn'>
    <div className='flex-1 flex flex-col gap-2 h-full'>
      <div className='flex gap-2'>
        <div><Input field='day' wrapperCssClass='flex gap-2' /></div>
        <div className='grow'><Input field='id_email' wrapperCssClass='flex gap-2' /></div>
      </div>
      {EMAIL?.is_approved ? null : <div className='alert alert-danger'>{T.translate('Email is not approved yet.')}</div>}
      <div>
        {EMAIL ? <>
          <div className='card'>
            <div className='card-header'>{T.translate('From')}: {EMAIL.SENDER_ACCOUNT?.name}</div>
            <div className='card-header'>{T.translate('Subject')}: {EMAIL.mail_subject}</div>
            <div className='card-body'>
              <iframe
                src="about:blank"
                className='w-full min-h-96'
                srcDoc={EMAIL.mail_body}
              />
            </div>
          </div>
          <button
            className='btn btn-primary m-2'
            onClick={() => { setShowFormEmail(true); }}
          >
            <span className='icon'><i className='fas fa-pencil'></i></span>
            <span className='text'>Edit email</span>
          </button>
        </> : <div className='alert alert-warning'>{T.translate('Select email to be sent on')} <b>{T.translate('day')} {day}</b></div>}
      </div>
    </div>
    <div className='flex-1'>
      <div className='card'>
        <div className='card-header'>{T.translate('Recipients')}</div>
        <div className='card-body'>
          <TableCampaignsSchedulesRecipients
            tag='table_campaign_schedule_recipients'
            parentForm={form}
            uid={form.uid + "_table_campaign_schedule_recipients"}
            idCampaignSchedule={form.id}
            readonly={true}
          ></TableCampaignsSchedulesRecipients>
        </div>
      </div>
    </div>
    {showFormEmail ? 
      <Modal type='centered large'>
        <FormEmail id={EMAIL.id} onClose={() => setShowFormEmail(false)}></FormEmail>
      </Modal>
    : null}
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
