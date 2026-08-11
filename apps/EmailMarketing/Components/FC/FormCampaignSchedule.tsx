import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import FormEmail from './FormEmail';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormCampaignScheduleProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\EmailMarketing\\Loader',
  'Components\\FormCampaignSchedule'
).translate;

/**
 * Title
 *
 * @var [type]
 */
const Title = (props: FormCampaignScheduleProps) => <>
  <small>{translate('Campaign')} » {translate('Scheduled email')}</small>
  <h2>{useRecordField('title') ?? '-'}</h2>
</>;

/**
 * TabDefault
 *
 * @var [type]
 */
const TabDefault = (props: FormCampaignScheduleProps) => {
  const idEmail: number = useRecordField('id_email');
  const day: number = useRecordField('day');

  return <div className='flex flex-col h-full'>
    <div className='flex gap-2'>
      <div><Input field='day' /></div>
      <div className='grow'><Input field='id_email' wrapperCssClass='flex gap-2' /></div>
    </div>
    <div className='mt-8 w-[80%] m-auto overflow-auto'>
      {idEmail > 0 ? <FormEmail id={idEmail} />
      : <div className='alert alert-warning'>Select email to be sent on <b>day {day}</b></div>}
    </div>
  </div>;
}

/**
 * FormCampaignSchedule
 *
 * @var [type]
 */
const FormCampaignSchedule = (props: FormCampaignScheduleProps) => {
  return <Form
   componentName='FormCampaignSchedule'
    parentApp='Hubleto/App/Community/EmailMarketing'
    model='Hubleto/App/Community/EmailMarketing/Models/CampaignSchedule'
    urlSlug='email-marketing/campaign/schedules'
    endpointParams={{saveRelations: ['TAGS'] }}
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

export default FormCampaignSchedule;
