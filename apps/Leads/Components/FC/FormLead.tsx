import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMeta, FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecord } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import CalendarTab, { CalendarTabContext } from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import LeadFormActivity from './LeadFormActivity';
import moment from "moment";
import PrintPreviewUi from '@hubleto/react-ui/components/fc/FormComponents/PrintPreviewUi';

import TableLeadHistory from '../TableLeadHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/FC/TableEmailClicks';

export interface FormLeadProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\Leads\\Loader',
  'Components\\FormLead'
).translate;

/**
 * Title
 *
 * @var [type]
 */
const Title = (props: FormLeadProps) => <>
  <small>{translate('Lead')}</small>
  <h2>{useRecord().title ?? '-'}</h2>
</>;

const PrintPreview = (props: FormLeadProps) => <PrintPreviewUi/>;

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
        <Input field='title' customInputProperties={{cssClass: 'text-2xl'}} />
        <Input field='email' />
        <Input field='phone' />
        <Input field='profile_link_1' />
        <Input field='profile_link_2' />
        <Input field='profile_link_3' />
        <Input field='source_channel' customInputProperties={{readonly: isClosed}} />
        <Input title={translate('Tags')}>
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
        <Input field='note' customInputProperties={{cssClass: 'bg-yellow-50 dark:bg-slate-600', readonly: isClosed}} />
        {status == 4 ? <Input field='lost_reason' customInputProperties={{readonly: isClosed}} />: null}
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
          <Input fieldame='price' customInputProperties={{ cssClass: 'text-2xl', readonly: isClosed }} />
          <Input fieldame='id_currency' />
        </div>
        <Input field='score' customInputProperties={{readonly: isClosed}} />
        <Input field='id_team' customInputProperties={{readonly: isClosed}} />
        <Input field='date_expected_close' customInputProperties={{readonly: isClosed}} />
        <Input field='id_customer' />
        <Input field='id_contact' />
        <Input field='shared_folder' customInputProperties={{readonly: isClosed}} />
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
        description: { type: "varchar", title: translate("Description")},
        change_date: { type: "date", title: translate("Change Date")},
      },
      inputs: {
        description: { type: "varchar", title: translate("Description"), readonly: true},
        change_date: { type: "date", title: translate("Change Date")},
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
    {...props}
    componentName='FormTeam'
    parentApp='Hubleto/App/Community/Leads'
    model='Hubleto/App/Community/Leads/Models/Lead'
    urlSlug='leads'
    customEndpointParams={{saveRelations: ['TAGS'] }}
    showWorkflowUi={true}
    showOwnerManagerUi={true}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}

    uiComponents={{
      title: <Title {...props} />,
      // printPreviewUi: <PrintPreview {...props} />,
      tabs: {
        default: { title: <b>{translate('Lead')}</b>, content: () => <TabDefault {...props} /> },
        calendar: { title: translate('Calendar'), content: () => <TabCalendar /> },
        email_clicks: { title: translate('Email Clicks'), content: () => <TabEmailClicks {...props} /> },
        tasks: { title: translate('Tasks'), content: () => <TabTasks {...props} /> },
        history: { icon: 'fas fa-clock-rotate-left', position: 'right', content: () => <TabHistory /> },
        timeline: { icon: 'fas fa-timeline', position: 'right', content: () => <TabTimeline {...props} /> },
      },
      // saveButton: () => <div>save</div>,
      // closeButton: () => <div>close</div>,
    }}
  ></Form>;
}

export default FormLead;


  // const FormTab = React.memo(({ record, activeTabUid }) => {
  //   const R = record;


  //     case 'documents':
  //       return <TableDocuments
  //         tag={"table_lead_document"}
  //         parentForm={this}
  //         uid={props.uid + "_table_lead_document"}
  //         junctionTitle='Lead'
  //         junctionModel='Hubleto/App/Community/Leads/Models/LeadDocument'
  //         junctionSourceColumn='id_lead'
  //         junctionSourceRecordId={R.id}
  //         junctionDestinationColumn='id_document'
  //       />;
  //     break;

  //     case 'history':

  //     break;

  //     case 'timeline':
  //       return 
  //     break;

  //   }
  // });