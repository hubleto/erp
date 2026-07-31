import React, { useState } from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormProps } from '@hubleto/react-ui/components/fc/Form';
import InputTags2 from '@hubleto/react-ui/components/cc/Inputs/Tags2';
import FormInput from '@hubleto/react-ui/components/cc/FormInput';
import CalendarTab, { CalendarTabContext } from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import LeadFormActivity from '../LeadFormActivity';
import moment from "moment";

import TableLeadHistory from '../TableLeadHistory';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/TableEmailClicks';

export interface FormLeadProps extends FormProps {}

/**
 * FormLead
 *
 * @var [type]
 */
const FormLead = (props: FormLeadProps) => {

  const translate = new Translator(
    'Hubleto\\App\\Community\\Leads\\Loader',
    'Components\\FormLead'
  ).translate;

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

    renderSubTitle={(form: any): React.JSX.Element => <small>{translate('Lead')}</small>}
    renderTitle={(form: any): React.JSX.Element => {
      return <>
        <small>{translate('Lead')}</small>
        <h2>{form.record.title ? form.record.title : '-'}</h2>
      </>;
    }}

    uiComponents={{
      title: <>
        <small>{translate('Lead')}</small>
        <h2>blabla</h2>
      </>
    }}

    renderTab={(form: any) => {
      const R = form.record;

      switch (form.activeTabUid) {
        case 'default':
          let nextActivity: any = null;
          let nextActivityDate: any = null;

          if (R.ACTIVITIES) {
            Object.keys(R.ACTIVITIES).map((key) => {
              if (nextActivityDate !== null) return;
              const activity = R.ACTIVITIES[key];
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
                {form.renderInputWrapper('title', {cssClass: 'text-2xl', readonly: R.is_closed})}
                {form.renderInputWrapper('email')}
                {form.renderInputWrapper('phone')}
                {form.renderInputWrapper('profile_link_1')}
                {form.renderInputWrapper('profile_link_2')}
                {form.renderInputWrapper('profile_link_3')}
                {form.renderInputWrapper('source_channel', {readonly: R.is_closed})}
                <FormInput title={ translate('Tags') }>
                  <InputTags2 {...form.getInputProps('tags_input')}
                    value={R.TAGS}
                    readonly={R.is_closed}
                    model='Hubleto/App/Community/Leads/Models/Tag'
                    targetColumn='id_lead'
                    sourceColumn='id_tag'
                    colorColumn='_LOOKUP_COLOR'
                    showSelect={false}
                    showTagButtons={true}
                    onChange={(input: any, value: any) => {
                      R.TAGS = value;
                      form.changeRecord(R);
                    }}
                    onNewTag={(title: string) => {
                      return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
                    }}
                  ></InputTags2>
                </FormInput>
                {form.renderInputWrapper('note', {cssClass: 'bg-yellow-50 dark:bg-slate-600', readonly: R.is_closed})}
                {R.status == 4 ? form.renderInputWrapper('lost_reason', {readonly: R.is_closed}): null}
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
                  {form.renderInputWrapper('price', { cssClass: 'text-2xl', readonly: R.is_closed ? true : false })}
                  {form.renderInputWrapper('id_currency')}
                </div>
                {form.renderInputWrapper('score', {readonly: R.is_closed})}
                {form.renderInputWrapper('id_team', {readonly: R.is_closed})}
                {form.renderInputWrapper('date_expected_close', {readonly: R.is_closed})}
                {form.renderInputWrapper('id_customer')}
                {form.renderInputWrapper('id_contact')}
                {form.renderInputWrapper('shared_folder', {readonly: R.is_closed})}
                {form.renderInputWrapper('date_created')}
              </div>
            </div>
          </>
        break;

        case 'calendar':
          return <CalendarTab
            parentForm={form}
            renderActivityForm={(calendarTab: CalendarTabContext) => {
              return <LeadFormActivity
                id={calendarTab.showIdActivity}
                isInlineEditing={true}
                description={{
                  defaultValues: {
                    id_lead: R.id,
                    id_contact: R.id_contact,
                    date_start: calendarTab.activityDate,
                    time_start: calendarTab.activityTime == "00:00:00" ? null : calendarTab.activityTime,
                    date_end: calendarTab.activityDate,
                    all_day: calendarTab.activityAllDay,
                    subject: calendarTab.activitySubject,
                  }
                }}
                idCustomer={R.id_customer}
                onClose={() => { calendarTab.setShowIdActivity(0) }}
                onSaveCallback={(form: any, saveResponse: any) => {
                  if (saveResponse.status == "success") {
                    calendarTab.setShowIdActivity(0);
                  }
                }}
              ></LeadFormActivity>;
            }}
          />
        break;

        case 'email_clicks':
          return <TableEmailClicks
            tag={"table_email_clicks"}
            parentForm={this}
            uid={props.uid + "_table_email_clicks"}
            email={R.email}
          />;
        break;

        case 'documents':
          return <TableDocuments
            tag={"table_lead_document"}
            parentForm={this}
            uid={props.uid + "_table_lead_document"}
            junctionTitle='Lead'
            junctionModel='Hubleto/App/Community/Leads/Models/LeadDocument'
            junctionSourceColumn='id_lead'
            junctionSourceRecordId={R.id}
            junctionDestinationColumn='id_document'
          />;
        break;

        case 'tasks':
          return <TableTasks
            tag={"table_lead_task"}
            parentForm={this}
            uid={props.uid + "_table_lead_task"}
            junctionTitle='Lead'
            junctionModel='Hubleto/App/Community/Leads/Models/LeadTask'
            junctionSourceColumn='id_lead'
            junctionSourceRecordId={R.id}
            junctionDestinationColumn='id_task'
          />;
        break;

        case 'history':

          if (R.HISTORY && R.HISTORY.length > 0) {
            if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
              R.HISTORY = R.HISTORY.reverse();
          }

          return <>
            <div className='card'>
              <div className='card-body [&_*]:whitespace-normal'>
                <TableLeadHistory
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
                ></TableLeadHistory>
              </div>
            </div>
          </>;
        break;

        case 'timeline':
          return form.renderTimeline([
            {
              data: (thisForm: any) => thisForm.state.record.ACTIVITIES,
              icon: 'fas fa-calendar',
              color: '#32678fff',
              timestampFormatter: (entry: any) => entry.date_start,
              valueFormatter: (entry: any) => entry.subject,
              userNameFormatter: (entry: any) => entry['_LOOKUP[id_owner]'],
            },
            { 
              data: (thisForm: any) => thisForm.state.record.WORKFLOW_HISTORY,
              icon: 'fas fa-timeline',
              color: '#8f3248ff',
              timestampFormatter: (entry: any) => entry.datetime_change,
              valueFormatter: (entry: any) => entry.WORKFLOW_STEP?.name ?? '---',
              userNameFormatter: (entry: any) => entry.USER?.nick,
            },
          ]);
        break;

      }
    }}
  ></Form>;
}

export default FormLead;