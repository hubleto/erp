import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecord } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import CalendarTab, { CalendarTabContext } from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import LeadFormActivity from './LeadFormActivity';
import moment from "moment";
import PrintPreviewUi from '@hubleto/react-ui/components/fc/FormComponents/PrintPreviewUi';

import TableLeadHistory from '../TableLeadHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/FC/TableEmailClicks';

export interface FormLeadProps extends FormProps {}

const T = new Translator(
  'Hubleto\\App\\Community\\Leads\\Loader',
  'Components\\FormLead'
);

/**
 * TabDefault
 *
 * @var [type]
 */
const TabDefault = (props: FormLeadProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const R = useRecord();
  const ACTIVITIES = R.ACTIVITIES;
  const TAGS = R.TAGS;
  const status = R.status;
  const isClosed = R.is_closed;

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

  return <>
    <div className='card card-body flex flex-col gap-2 md:flex-row'>
      <div className='grow'>
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
      <div className='border-l border-gray-200'></div>
      <div className='grow'>
        {form.id > 0 ? <>
          {nextActivityDate ?
            <div className='block alert alert-success'>
              <i className='fas fa-calendar mr-2'></i>
              Next activity is planned for <b>{nextActivityDate.format('YYYY-MM-DD')}</b>.<br/>
              <br/>
              <i>{nextActivity.subject}</i>
            </div>
          : <div className='block alert alert-danger'>
              <i className='fas fa-calendar mr-2'></i>
              No future activity is planned.
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
        <Input field='date_created' />
      </div>
    </div>
  </>
}

/**
 * TabCalendar
 *
 * @var [type]
 */
const TabCalendar = () => <CalendarTab
  calendarSource='leads'
  externalIdColumn='idLead'
  logActivityEndpoint='leads/api/log-activity'
  renderActivityForm={(calendarTab: any) => {
    const R = useRecord();
    const id = R.id;
    const idCustomer = R.id_customer;
    const idContact = R.id_contact;

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
      onAfterSaveRecord={(form: any, saveResponse: any) => {
        if (saveResponse.status == "success") {
          calendarTab.setShowIdActivity(0);
        }
      }}
    ></LeadFormActivity>;
  }}
></CalendarTab>;

/**
 * TabEmailClicks
 *
 * @var [type]
 */
const TabEmailClicks = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  return <TableEmailClicks
    tag={"table_email_clicks"}
    parentForm={form}
    uid={form.uid + "_table_email_clicks"}
  />;
};


/**
 * TabTasks
 *
 * @var [type]
 */
const TabTasks = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  
  return <TableTasks
    tag={"table_lead_task"}
    parentForm={this}
    uid={props.uid + "_table_lead_task"}
    junctionTitle='Lead'
    junctionModel='Hubleto/App/Community/Leads/Models/LeadTask'
    junctionSourceColumn='id_lead'
    junctionSourceRecordId={form.id}
    junctionDestinationColumn='id_task'
  />;
}

/**
 * TabHistory
 *
 * @var [type]
 */
const TabHistory = (props: FormLeadProps) => {
  const R = useRecord();
  
  if (R.HISTORY && R.HISTORY.length > 0) {
    if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
      R.HISTORY = R.HISTORY.reverse();
  }

  return <TableLeadHistory
    uid={props.uid + "_table_lead_history"}
    data={{ records: R.HISTORY }}
    descriptionSource="props"
    onRowClick={(table) => {}}
    description={{
      permissions: {
        canCreate: false,
        canDelete: false,
        canRead: true,
        canUpdate: false,
      },
      ui: {
        showFooter: false,
        showHeader: false,
      },
      columns: {
        description: { type: "varchar", title: T.translate("Description")},
        change_date: { type: "date", title: T.translate("Change Date")},
      },
      inputs: {
        description: { type: "varchar", title: T.translate("Description"), readonly: true},
        change_date: { type: "date", title: T.translate("Change Date")},
      },
    }}
    readonly={true}
  ></TableLeadHistory>;
}

/**
 * TabTimeline
 *
 * @var [type]
 */
const TabTimeline = (props: FormLeadProps) => {
  const form = React.useContext(FormMetaContext);
  const R = useRecord();
  
  return form.renderTimeline([
    {
      data: (thisForm: any) => R.ACTIVITIES,
      icon: 'fas fa-calendar',
      color: '#32678fff',
      timestampFormatter: (entry: any) => entry.date_start,
      valueFormatter: (entry: any) => entry.subject,
      userNameFormatter: (entry: any) => entry['_LOOKUP[id_owner]'],
    },
    { 
      data: (thisForm: any) => R.WORKFLOW_HISTORY,
      icon: 'fas fa-timeline',
      color: '#8f3248ff',
      timestampFormatter: (entry: any) => entry.datetime_change,
      valueFormatter: (entry: any) => entry.WORKFLOW_STEP?.name ?? '---',
      userNameFormatter: (entry: any) => entry.USER?.nick,
    },
  ]);
}

/**
 * FormLead
 *
 * @var [type]
 */
const FormLead = (props: FormLeadProps) => {
  return <Form
    componentName='FormTeam'
    parentApp='Hubleto/App/Community/Leads'
    model='Hubleto/App/Community/Leads/Models/Lead'
    urlSlug='leads'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    tabs={{
      default: { title: <b>{T.translate('Lead')}</b>, content: () => <TabDefault {...props} /> },
      calendar: { title: T.translate('Calendar'), content: () => <TabCalendar /> },
      email_clicks: { title: T.translate('Email Clicks'), content: () => <TabEmailClicks {...props} /> },
      tasks: { title: T.translate('Tasks'), content: () => <TabTasks {...props} /> },
      history: { icon: 'fas fa-clock-rotate-left', position: 'right', content: () => <TabHistory /> },
      timeline: { icon: 'fas fa-timeline', position: 'right', content: () => <TabTimeline {...props} /> },
    }}
    title={{field: 'title', sub: T.translate('Lead')}}
    {...props}
  ></Form>;
}

export default FormLead;
