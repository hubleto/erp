import React, { Component } from 'react';
import { type FormMeta, type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableCampaignsSchedulesRecipients from './TableCampaignsSchedulesRecipients';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import FormEmail from './FormEmail';

export interface FormCampaignScheduleProps extends FormProps {
  idCampaign?: number,
  parentCampaign?: any,
}

const componentName = 'FormCampaignSchedule';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormCampaignSchedule');

/** TabDefault */
const TabDefault = (props: FormCampaignScheduleProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const EMAIL: any = useRecordField('EMAIL');
  const day: number = useRecordField('day');
  const isNurturing = props.parentCampaign?.type == 1;
  const isNewsletter = props.parentCampaign?.type == 2;

  const [showFormEmail, setShowFormEmail] = React.useState(false);

  console.log('TabDefault', props.parentCampaign);
  console.log('TabDefault', isNurturing, isNewsletter);

  return <div className='flex-dyn'>
    <div className='flex-1 flex flex-col gap-2 h-full'>
      {form.id < 0 ? 
        <div className='alert alert-info text-center w-1/2 m-auto mt-16 text-lg'>{T.translate('Click `Add` to create empty email. You will be able to edit it later.')}</div>
      : <>
        {isNurturing ?
          <div className='flex gap-2'>
            <div><Input field='day' wrapperCssClass='flex gap-2' /></div>
            <div className='grow'><Input field='id_email' wrapperCssClass='flex gap-2' /></div>
          </div>
        : null}
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
        {EMAIL?.is_approved ? null : <div className='alert alert-danger'>{T.translate('Email is not approved yet.')}</div>}
      </>}
    </div>
    {form.id <= 0 ? null :
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
    }
    {showFormEmail ? 
      <Modal type='centered large'>
        <FormEmail id={EMAIL.id} onClose={() => setShowFormEmail(false)}></FormEmail>
      </Modal>
    : null}
  </div>;
}

/** FormCampaignSchedule */
const FormCampaignSchedule = (props: FormCampaignScheduleProps) => {
  const isNurturing = props.parentCampaign?.type == 1;
  const isNewsletter = props.parentCampaign?.type == 2;

  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CampaignSchedule'}
    urlSlug='email-marketing/campaign/schedules'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    title={{main: <>{props.parentCampaign?.title} » {isNurturing ? T.translate('Schedule email') : (isNewsletter ? T.translate('Newsletter email') : null)}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormCampaignSchedule;
