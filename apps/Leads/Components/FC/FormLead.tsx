import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormDescriptionContext, FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField, FormRecordStoreContext } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import FormInput from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags2 from '@hubleto/react-ui/components/cc/Inputs/Tags2';
// import FormInput from '@hubleto/react-ui/components/cc/FormInput';
import CalendarTab, { CalendarTabContext } from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import LeadFormActivity from '../LeadFormActivity';
import moment from "moment";

import TableLeadHistory from '../TableLeadHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/TableEmailClicks';

export interface FormLeadProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\Leads\\Loader',
  'Components\\FormLead'
).translate;

const FormTitle = () => {
  return <>
    <small>{translate('Lead')}</small>
    <h2>{useRecordField(r => r.title) ?? '-'}</h2>
  </>;
};

const FormTabContent = () => {
  const R = React.useContext(FormRecordStoreContext);
  const description = React.useContext(FormDescriptionContext);
  const meta = React.useContext(FormMetaContext);

  return <>
    <div className='card card-body flex flex-col gap-2 md:flex-row'>
      <div className='grow'>
        <FormInput name='title' />
        <FormInput name='email' />
        <FormInput name='phone' />
        <FormInput name='profile_link_1' />
        <FormInput name='profile_link_2' />
        <FormInput name='profile_link_3' />
        <FormInput name='source_channel' customInputProps={{readonly: useRecordField(r => r.is_closed)}} />
      </div>
    </div>
  </>;
};

/**
 * FormLead
 *
 * @var [type]
 */
const FormLead = (props: FormLeadProps) => {

  return <Form
    {...props}
    componentName='FormTeam'
    parentApp='Hubleto/App/Community/Leads'
    model='Hubleto/App/Community/Leads/Models/Lead'
    urlSlug='leads'
    getContentClassName={(form: any): string => form.record.is_closed ? 'bg-slate-100' : ''}
    customEndpointParams={{saveRelations: ['TAGS'] }}
    getTabs={(form: any) => [
      { uid: 'default', title: <b>{translate('Lead')}</b> },
      { uid: 'todo', title: translate('Todo') },
      { uid: 'calendar', title: translate('Calendar') },
      { uid: 'email_clicks', title: translate('Email Clicks') },
      { uid: 'tasks', title: translate('Tasks'), showCountFor: 'TASKS' },
      { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
      { uid: 'timeline', icon: 'fas fa-timeline', position: 'right' },
      ...form.getCustomTabs()
    ]}

    showWorkflowUi={true}
    showOwnerManagerUi={true}

    uiComponents={{
      title: () => <FormTitle />,
      tabContent: () => <FormTabContent />,
    }}

  ></Form>;
}

export default FormLead;