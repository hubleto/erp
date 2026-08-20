import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import CalendarTab from '@hubleto/apps/Calendar/Components/FC/CalendarTab';
import moment from "moment";

import TableTasks from '@hubleto/apps/Tasks/Components/FC/TableTasks';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/FC/TableEmailClicks';
import Table from '@hubleto/react-ui/components/fc/Table';
import CalendarTabFormActivity from '@hubleto/apps/Calendar/Components/FC/CalendarTabFormActivity';

export interface FormLeadProps extends FormProps {}

const T = new Translator(
  'Hubleto\\App\\Community\\Leads\\Loader',
  'Components\\FormLead'
);

/** TabDefault */
const TabDefault = (props: FormLeadProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', {});
  const TAGS: Array<any> = useRecordField('TAGS', []);
  const status: number = useRecordField('status', 0);
  const isClosed: boolean = useRecordField('is_closed', false);

  let nextActivity: any = null;
  let nextActivityDate: any = null;

  if (ACTIVITIES) {
    Object.keys(ACTIVITIES).map((key) => {
      if (nextActivityDate !== null) return;
      const activity = ACTIVITIES[key];
      const dateStart = moment(activity.date_start);
      if (!activity.completed && dateStart.isAfter()) {
        nextActivity = activity;
        nextActivityDate = dateStart;
      }
    });
  }

  return <div className='flex flex-col gap-2 md:flex-row'>
    <div className='flex-1'>
      <Input field='title' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='email' />
      <Input field='phone' />
      <Input field='profile_link_1' />
      <Input field='profile_link_2' />
      <Input field='profile_link_3' />
      <Input field='source_channel' customInputProps={{readonly: isClosed}} />
      <Input title={T.translate('Tags')}>
        <InputTags
          field='TAGS'
          value={TAGS}
          readonly={isClosed}
          model='Hubleto/App/Community/Leads/Models/Tag'
          targetColumn='id_lead'
          sourceColumn='id_tag'
          colorColumn='_LOOKUP_COLOR'
          showSelect={false}
          showTagButtons={true}
          onChange={(input: any, value: any) => {
            form.changeField(input, value);
          }}
          onNewTag={(title: string) => {
            return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
          }}
        ></InputTags>
      </Input>
      <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 dark:bg-slate-600', readonly: isClosed}} />
      {status == 4 ? <Input field='lost_reason' customInputProps={{readonly: isClosed}} />: null}
    </div>
    <div className='flex-1'>
      {form.id > 0 ? <>
        {nextActivityDate ?
          <div className='block alert alert-success'>
            <i className='fas fa-calendar mr-2'></i>
            Next follow-up is planned for <b>{nextActivityDate.format('YYYY-MM-DD')}</b>.<br/>
            <br/>
            <i>{nextActivity.subject}</i>
          </div>
        : <div className='block alert alert-danger'>
            <i className='fas fa-calendar mr-2'></i>
            No future follow-up is planned.
          </div>
        }
      </> : null}
      <div className='flex flex-row *:w-1/2'>
        <Input fieldame='price' customInputProps={{ cssClass: 'text-2xl', readonly: isClosed }} />
        <Input fieldame='id_currency' />
      </div>
      <Input field='score' customInputProps={{readonly: isClosed}} />
      <Input field='id_team' customInputProps={{readonly: isClosed}} />
      <Input field='date_expected_close' customInputProps={{readonly: isClosed}} />
      <Input field='id_customer' />
      <Input field='id_contact' />
      <Input field='shared_folder' customInputProps={{readonly: isClosed}} />
    </div>
  </div>;
}

/** TabCalendar */
const TabCalendar = (props: FormProps) => <CalendarTab
  loadEventsEndpoint={'calendar/api/get-calendar-events?calendar=leads&idLead=' + props.id}
  logActivityEndpoint={'leads/api/log-activity?idLead=' + props.id}
  renderActivityForm={(calendarTab: any) => {
    return <CalendarTabFormActivity
      calendarTab={calendarTab}
      customInputFields={['id_lead']}
      defaultValues={{id_lead: props.id}}
      model='Hubleto/App/Community/Leads/Models/LeadActivity'
    ></CalendarTabFormActivity>;
  }}
></CalendarTab>;

/** TabEmailClicks */
const TabEmailClicks = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  return <TableEmailClicks
    tag={"table_email_clicks"}
    parentForm={form}
    uid={form.uid + "_table_email_clicks"}
  />;
};


/** TabTasks */
const TabTasks = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  
  return <TableTasks
    tag={"table_lead_task"}
    parentForm={form}
    uid={props.uid + "_table_lead_task"}
    junctionTitle='Lead'
    junctionModel='Hubleto/App/Community/Leads/Models/LeadTask'
    junctionSourceColumn='id_lead'
    junctionSourceRecordId={form.id}
    junctionDestinationColumn='id_task'
  />;
}

/** TabHistory */
const TabHistory = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  return <Table
    parentForm={form}
    uid={props.uid + "_table_lead_history"}
    model={'Hubleto/App/Community/Leads/Models/LeadHistory'}
    endpointParams={{idLead: props.id}}
    readonly={true}
  ></Table>;
}

/** TabTimeline */
const TabTimeline = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', {});
  const WORKFLOW_HISTORY: any = useRecordField('WORKFLOW_HISTORY', {});
  
  return form.renderTimeline([
    {
      data: (thisForm: any) => ACTIVITIES,
      icon: 'fas fa-calendar',
      color: '#32678fff',
      timestampFormatter: (entry: any) => entry.date_start,
      valueFormatter: (entry: any) => entry.subject,
      userNameFormatter: (entry: any) => entry['_LOOKUP[id_owner]'],
    },
    { 
      data: (thisForm: any) => WORKFLOW_HISTORY,
      icon: 'fas fa-timeline',
      color: '#8f3248ff',
      timestampFormatter: (entry: any) => entry.datetime_change,
      valueFormatter: (entry: any) => entry.WORKFLOW_STEP?.name ?? '---',
      userNameFormatter: (entry: any) => entry.USER?.nick,
    },
  ]);
}

/** FormLead */
const FormLead = (props: FormLeadProps) => {
  return <Form
    componentName='FormTeam'
    parentApp='Hubleto/App/Community/Leads'
    model='Hubleto/App/Community/Leads/Models/Lead'
    urlSlug='leads'
    endpointParams={{saveRelations: ['TAGS'] }}
    tabs={{
      default: { title: <b>{T.translate('Lead')}</b>, content: () => <TabDefault {...props} /> },
      calendar: { title: T.translate('Calendar'), content: () => <TabCalendar {...props} /> },
      email_clicks: { title: T.translate('Email Clicks'), content: () => <TabEmailClicks {...props} /> },
      tasks: { title: T.translate('Tasks'), content: () => <TabTasks {...props} /> },
      history: { icon: 'fas fa-clock-rotate-left', position: 'right', content: () => <TabHistory /> },
      timeline: { icon: 'fas fa-timeline', position: 'right', content: () => <TabTimeline {...props} /> },
    }}
    title={{fields: ['title'], sub: T.translate('Lead')}}
    {...props}
  ></Form>;
}

export default FormLead;
