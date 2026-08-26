import React, { useState } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import moment, { Moment } from "moment";
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import request from '@hubleto/react-ui/core/Request';
import TableTasks from '@hubleto/apps/Tasks/Components/FC/TableTasks';
import CalendarTab from '@hubleto/apps/Calendar/Components/FC/CalendarTab';
import DealCalendarActivityForm from './DealCalendarActivityForm';
import TableItems from './TableItems';

export interface FormDealProps extends FormProps {}

const componentName = 'FormDeal'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Deals';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormDealProps) => {
  const form = React.useContext(FormMetaContext);
  const isClosed: boolean = useRecordField('is_closed', false);
  const isNewCustomer: boolean = useRecordField('is_new_customer', false);
  const businessType: number = useRecordField('business_type', 0);
  const dealResult: number = useRecordField('deal_result', 0);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', []);
  const LEADS: any = useRecordField('LEADS', []);
  const CONTACT: any = useRecordField('CONTACT', {});
  const ITEMS: any = useRecordField('ITEMS', []);

  const [selectParentLead, setSelectParentLead] = useState(false);

  let nextActivity = null;
  let nextActivityDate = null;

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

  const inputsColumnLeft = <>
    <Input title={T.translate("Lead")}>
      {selectParentLead ? <LookupInput
        model='Hubleto/App/Community/Leads/Models/Lead'
        cssClass='font-bold'
        onChange={(input: any, value: any) => {
          request.post(
            'deals/api/set-parent-lead',
            { idDeal: form.id, idLead: value },
            {},
            (data: any) => { setSelectParentLead(false); }
          )
        }}
      ></LookupInput>
      : <>
        {LEADS ? LEADS.map((item, key) => {
          if (!item.LEAD) return null;
          return (item.LEAD ? <a
            key={key}
            className='badge'
            href={globalThis.hubleto.config.projectUrl + '/leads/' + item.LEAD.id}
            target='_blank'
          >#{item.LEAD.id}</a> : '#');
        }) : null}
        <button
          className='btn btn-small btn-transparent'
          onClick={() => { setSelectParentLead(true); }}
        >
          <span className='text'>{T.translate('Select parent lead')}</span>
        </button>
      </>}
    </Input>
    <Input field='identifier' customInputProps={{cssClass: 'text-2xl', readonly: isClosed}} />
    <Input field='title' customInputProps={{cssClass: 'text-2xl', readonly: isClosed}} />
    <Input field='version' />
    <Input field='id_customer' />
    <Input field='id_contact' />
    <div>
      {CONTACT && CONTACT.VALUES ? <div className="text-sm p-2 card">
        {CONTACT.VALUES.map((item, key) => {
          return <div key={key}>{item.value}</div>;
        })}
      </div> : null}
    </div>
    <Input field='id_currency' />
    <Input field='price_excl_vat' customInputProps={{
      cssClass: 'text-2xl',
      readonly: (ITEMS && ITEMS.length > 0) ? true : false,
    }} />
    <Input field='price_incl_vat' customInputProps={{
      readonly: (ITEMS && ITEMS.length > 0) ? true : false,
    }} />
    {ITEMS && ITEMS.length > 0 ?
      <div className='badge badge-warning'>
        <span className='icon mr-2'><i className='fas fa-warning'></i></span>
        <span className='text'>{T.translate('Price is calculated from items.')}</span>
      </div>
    : <></>}
    <Input field='date_expected_close' customInputProps={{readonly: isClosed}} />
  </>;

  const inputsColumnRight = <>
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
          No follow-up in your calendar.
        </div>
      }
    </> : null}
    <Input field='source_channel' customInputProps={{readonly: isClosed, uiStyle: 'buttons'}} />
    <Input field='is_new_customer' customInputProps={{readonly: isClosed, yesText: 'New customer', onChange: (input: any, value: any) => {
      if (isNewCustomer) {
        form.changeRecord({
          is_new_customer: value,
          business_type: 1 /* New */
        });
      }
    }}} />
    <Input field='business_type' customInputProps={{uiStyle: 'buttons', readonly: isClosed, onChange: (input: any, value: any) => {
      if (businessType == 2 /* Existing */) {
        form.changeRecord({business_type: value, is_new_customer: false});
      }
    }}} />
    <div className='flex-dyn w-full'>
      <Input field='deal_result' customInputProps={{
        uiStyle: 'buttons',
        readonly: isClosed,
        onChange: (input: any, value: any) => {
          form.changeRecord({deal_result: value, lost_reason: null});
        }
      }} />
      {dealResult == 2 ? <Input field='lost_reason' customInputProps={{readonly: isClosed}} />: null}
    </div>
    <Input field='note' customInputProps={{cssClass: 'border border-orange-200', readonly: isClosed}} />
  </>;

  return <div className='flex-dyn'>
    <div className='grow max-w-1/2'>{inputsColumnLeft}</div>
    <div className='grow max-w-1/2'>{inputsColumnRight}</div>
  </div>;
}

/** TabItems */
const TabItems = (props: FormDealProps) => {
  const form = React.useContext(FormMetaContext);

  return <div className='w-full h-full overflow-x-auto'>
    <div><Input field='description_before' /></div>
    <TableItems
      uid={props.uid + "_table_deal_items"}
      tag={"deal_items"}
      parentForm={form}
      idDeal={form.id}
      descriptionSource='both'
    ></TableItems>
    <div><Input field='description_after' /></div>
  </div>;
}

/** TabDocuments */
const TabDocuments = (props: FormDealProps) => {
  const isClosed: boolean = useRecordField('is_closed', false);
  const sharedFolder: string = useRecordField('shared_folder', '');

  let iframeUrl = '';

  try {
    let url = new URL(sharedFolder ?? '');
    
    if (
      url.hostname == 'drive.google.com'
      && url.pathname.indexOf('/drive/folders') == 0
    ) {

      // for Google Drive, replacing
      // https://drive.google.com/drive/folders/FOLDER_ID
      // with https://drive.google.com/embeddedfolderview?id=FOLDER_ID
      // makes the folder embeddable

      iframeUrl = 'https://drive.google.com/embeddedfolderview'
        + '?id=' + url.pathname.replace('/drive/folders/', '')
        + '&authuser=0'
      ;

    } else {
      iframeUrl = sharedFolder ?? '';
    }
  } catch (e) {
  }

  return <div className='flex flex-col gap-2 h-full'>
    <Input field='shared_folder' customInputProps={{readonly: isClosed}} />
    <iframe
      className='w-full h-full shadow-sm'
      src={iframeUrl}
    ></iframe>
  </div>;
}

/** TabCalendar */
const TabCalendar = (props: FormProps) =>  {
  const form = React.useContext(FormMetaContext);
  return <CalendarTab
    loadEventsEndpoint={'calendar/api/get-calendar-events?calendar=deals&idDeal=' + form.id}
    logActivityEndpoint={'deals/api/log-activity?idDeal=' + form.id}
    renderActivityForm={(calendarTab: any) => {
      return <DealCalendarActivityForm idDeal={form.id} calendarTab={calendarTab}></DealCalendarActivityForm>;
    }}
  ></CalendarTab>;
}

/** TabTasks */
const TabTasks = (props: FormDealProps) => {
  const form = React.useContext(FormMetaContext);
  return <TableTasks
    tag={"table_deal_task"}
    parentForm={form}
    uid={props.uid + "_table_deal_task"}
    junctionTitle='Deal'
    junctionModel='Hubleto/App/Community/Deals/Models/DealTask'
    junctionSourceColumn='id_deal'
    junctionSourceRecordId={form.id}
    junctionDestinationColumn='id_task'
  />;
}

/** TabHistory */
const TabHistory = (props: FormDealProps) => {
  const form = React.useContext(FormMetaContext);
  const HISTORY: any = useRecordField('HISTORY', {});

  return JSON.stringify(HISTORY);
}

/** TabTimeline */
const TabTimeline = (props: FormDealProps) => {
  const form = React.useContext(FormMetaContext);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', {});
  const WORKFLOW_HISTORY: any = useRecordField('WORKFLOW_HISTORY', {});

  return form.renderTimeline([
    {
      data: (thisForm) => ACTIVITIES,
      icon: 'fas fa-calendar',
      color: '#32678fff',
      timestampFormatter: (entry) => entry.date_start,
      valueFormatter: (entry) => entry.subject,
      userNameFormatter: (entry) => entry['_LOOKUP[id_owner]'],
    },
    { 
      data: (thisForm) => WORKFLOW_HISTORY,
      icon: 'fas fa-timeline',
      color: '#8f3248ff',
      timestampFormatter: (entry) => entry.datetime_change,
      valueFormatter: (entry) => entry.WORKFLOW_STEP?.name ?? '---',
      userNameFormatter: (entry) => entry.USER?.nick,
    },
  ]);
}



/** FormDeal */
const FormDeal = (props: FormDealProps) => {

  const calculateWeightedProfit = (probability: number, price: number) => {
    return (probability / 100) * price;
  }

  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Deal'}
    urlSlug='deals'
    endpointParams={{}}
    title={{fields: ['identifier', 'title'], sub: T.translate('Deal')}}
    tabs={{
      default: {title: <b>{T.translate('Deal')}</b>, content: () => <TabDefault {...props} />},
      items: {title: T.translate('Items'), content: () => <TabItems {...props} />},
      documents: {title: T.translate('Documents'), content: () => <TabDocuments {...props} />},
      calendar: {title: T.translate('Calendar'), content: () => <TabCalendar {...props} />},
      tasks: {title: T.translate('Tasks'), content: () => <TabTasks {...props} />},
      history: {icon: 'fas fa-clock-rotate-left', content: () => <TabHistory {...props} />},
      timeline: {icon: 'fas fa-timeline', content: () => <TabTimeline {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormDeal;
