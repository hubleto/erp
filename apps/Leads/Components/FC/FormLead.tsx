import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormDescriptionContext, FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField, FormRecordStoreContext } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import FormInput from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags2 from '@hubleto/react-ui/components/cc/Inputs/Tags2';
// import FormInput from '@hubleto/react-ui/components/cc/FormInput';
import CalendarTab, { CalendarTabContext } from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import LeadFormActivity from './LeadFormActivity';
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

const Title = (props: FormLeadProps) => <>
  <small>{translate('Lead')}</small>
  <h2>{useRecordField(r => r.title) ?? '-'}</h2>
</>;

const TabDefault = (props: FormLeadProps) => <div className='card card-body flex flex-col gap-2 md:flex-row'>
  <div className='grow'>
    <FormInput name='title' />
    <FormInput name='email' />
    <FormInput name='phone' />
    <FormInput name='profile_link_1' />
    <FormInput name='profile_link_2' />
    <FormInput name='profile_link_3' />
    <FormInput name='source_channel' customInputProps={{readonly: useRecordField(r => r.is_closed)}} />
  </div>
</div>;

const TabTodo = () => <>TabTodo</>;
const TabCalendar = () => <CalendarTab
  renderActivityForm={(calendarTab: any) => {
    const id = useRecordField(r => r.id);
    const idCustomer = useRecordField(r => r.id_customer);
    const idContact = useRecordField(r => r.id_contact);

    console.log('calendarTab', calendarTab, calendarTab.showIdActivity)
    
    return <LeadFormActivity
        id={calendarTab.showIdActivity}
        description={{
          defaultValues: {
            id_lead: id,
            id_contact: idContact,
            date_start: calendarTab.activityDate,
            time_start: calendarTab.activityTime == "00:00:00" ? null : calendarTab.activityTime,
            date_end: calendarTab.activityDate,
            all_day: calendarTab.activityAllDay,
            subject: calendarTab.activitySubject,
          }
        }}
        idCustomer={idCustomer}
        idContact={idContact}
        onClose={() => { calendarTab.setShowIdActivity(0) }}
        // onSaveCallback={(form: any, saveResponse: any) => {
        //   if (saveResponse.status == "success") {
        //     calendarTab.setShowIdActivity(0);
        //   }
        // }}
      ></LeadFormActivity>
    ;
  }}
></CalendarTab>;
const TabEmailClicks = () => <>TabEmailClicks</>;
const TabTasks = () => <>TabTasks</>;
const TabHistory = () => <>TabHistory</>;
const TabTimeline = () => <>TabTimeline</>;

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
    customEndpointParams={{saveRelations: ['TAGS'] }}
    showWorkflowUi={true}
    showOwnerManagerUi={true}

    uiComponents={{
      title: <Title {...props} />,
      tabs: {
        default: { title: <b>{translate('Lead')}</b>, content: () => <TabDefault {...props} /> },
        todo: { title: translate('Todo'), content: () => <TabTodo /> },
        calendar: { title: translate('Calendar'), content: () => <TabCalendar /> },
        email_clicks: { title: translate('Email Clicks'), content: () => <TabEmailClicks /> },
        tasks: { title: translate('Tasks'), content: () => <TabTasks /> },
        history: { icon: 'fas fa-clock-rotate-left', position: 'right', content: () => <TabHistory /> },
        timeline: { icon: 'fas fa-timeline', position: 'right', content: () => <TabTimeline /> },
      },
      // saveButton: () => <div>save</div>,
      // closeButton: () => <div>close</div>,
    }}

  ></Form>;
}

export default FormLead;