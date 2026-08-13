import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import CalendarTab from '@hubleto/react-ui/components/fc/FormComponents/CalendarTab';
import CustomerFormActivity from './customerFormActivity';
import TableContacts from '@hubleto/apps/Contacts/Components/FC/TableContacts';

// import InputTags2 from "@hubleto/react-ui/components/cc/Inputs/Tags2";
// import FormInput from "@hubleto/react-ui/components/cc/FormInput";
// import CustomerFormActivity, {CustomerFormActivityProps, CustomerFormActivityState} from "./CustomerFormActivity";
// import ModalForm from "@hubleto/react-ui/components/cc/ModalForm";
// import { FormDealState } from "../../Deals/Components/FormDeal";
// import TableDocuments from '@hubleto/apps/Documents/Components/FC/TableDocuments';
// import FormDocument, {FormDocumentProps} from "../../Documents/Components/FC/FormDocument";
// import Calendar from '../../Calendar/Components/Calendar'
// import request from "@hubleto/react-ui/core/Request";
// import { FormProps, FormState } from "@hubleto/react-ui/components/cc/Form";
// import moment from "moment";

export interface FormCustomerProps extends FormProps {}

const componentName = 'FormCustomer'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Customers';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormCustomerProps) => {
  const form = React.useContext(FormMetaContext);
  const streetLine1: string = useRecordField('street_line_1', '');
  const streetLine2: string = useRecordField('street_line_2', '');
  const postalCode: string = useRecordField('postal_code', '');
  const city: string = useRecordField('city', '');
  const region: string = useRecordField('region', '');
  const COUNTRY: any = useRecordField('COUNTRY', {});
  const TAGS: any = useRecordField('TAGS', {});

  let mapAddress = '';
  if (streetLine1 != '' && city != '' && COUNTRY && COUNTRY.name != '') {
    mapAddress = streetLine1 + ', ' + postalCode + ' ' + city + ', ' + region + ', ' + COUNTRY.name;
  }

  return <>
    <div className='flex flex-col md:flex-row gap-2'>
      <div className='flex-2 card'>
        <div className="card-body flex flex-col md:flex-row gap-2">
          <div>
            <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
            <Input field='identifier' />
            <Input field='company_id' />
            <Input field='street_line_1' />
            <Input field='street_line_2' />
            <Input field='postal_code' />
            <Input field='city' />
            <Input field='region' />
            <Input field='id_country' />
            <div className="flex justify-between">
              {mapAddress == '' ? null :
                <div>
                  <a
                    href={"https://maps.google.com/?q=" + encodeURIComponent(mapAddress)}
                    target="_blank"
                    className="btn btn-transparent"
                  >
                    <span className="icon"><i className="fas fa-map"></i></span>
                    <span className="text">{T.translate("Show on map")}</span>
                  </a>
                </div>
              }
            </div>
            <Input field='shared_folder' />
          </div>
          <div>
            <Input field='id_owner' />
            <Input field='id_manager' />
            <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 dark:bg-slate-600'}} />
            <Input field='tax_id' />
            <Input field='vat_id' />
            <Input field='date_created' />
            <Input field='is_active' customInputProps={{yesText: T.translate('Active')}} />
            <Input title={T.translate('Tags')}>
              <InputTags
                field='TAGS'
                value={TAGS}
                model={parentApp + '/Models/Tag'}
                targetColumn='id_customer'
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
          </div>
        </div>
      </div>
      {props.id > 0 ? <div>
        <TableContacts
          uid={props.uid + "_table_contacts"}
          parentForm={this}
          showAsCards={true}
          idCustomer={props.id}
        ></TableContacts>
      </div> : null}
    </div>
  </>
}

/** TabCalendar */
const TabCalendar = (props: FormCustomerProps) => <CalendarTab
  renderActivityForm={(calendarTab: any) => {
    const idCustomer: number = useRecordField('id_customer');

    return <CustomerFormActivity
      id={calendarTab.showIdActivity}
      description={{
        defaultValues: {
          id_customer: props.id,
          date_start: calendarTab.activityDate,
          time_start: calendarTab.activityTime == "00:00:00" ? null : calendarTab.activityTime,
          date_end: calendarTab.activityDate,
          all_day: calendarTab.activityAllDay,
          subject: calendarTab.activitySubject,
        }
      }}
      idCustomer={idCustomer}
      onClose={() => { calendarTab.setShowIdActivity(0) }}
      onAfterSaveRecord={(form: any, saveResponse: any) => {
        if (saveResponse.status == "success") {
          calendarTab.setShowIdActivity(0);
        }
      }}
    ></CustomerFormActivity>;
  }}
></CalendarTab>;

/** FormCustomer */
const FormCustomer = (props: FormCustomerProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Customer'}
    urlSlug='customers'
    endpointParams={{saveRelations: ['TAGS']}}
    // onAfterFormInitialized={(form: any) => {}}
    onBeforeSaveRecord={(form: FormMeta, record: any) => {
      if (record.tax_id) record.tax_id = record.tax_id.replace(/\s+/g, "");
      if (record.vat_id) record.vat_id = record.vat_id.replace(/\s+/g, "");
      if (record.company_id) record.company_id = record.company_id.replace(/\s+/g, "");
      return record;
    }}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{field: 'name', sub: T.translate('Customer')}}
    tabs={{
      default: {title: <b>{T.translate('Customer')}</b>, content: () => <TabDefault {...props} />},
      calendar: {title: T.translate('Calendar'), content: () => <TabCalendar {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormCustomer;

//   refLogActivityInput: any;
//   refDocumentModal: any;
//   refDocumentForm: any;
//   refActivityModal: any;
//   refActivityForm: any;
//         ...this.getCustomTabs()
//   }

//   onAfterFormInitialized(): void {
//     request.get(
//       'api/table/describe',
//       {
//         model: 'Hubleto/App/Community/Contacts/Models/Value',
//         idContact: -1,
//       },
//       (description: any) => {
//         this.setState({tableValuesDescription: description} as FormCustomerState);
//       }
//     );
//   }

//   renderDocumentForm(): React.JSX.Element{
//     return (
//       <ModalForm
//         ref={this.refDocumentModal}
//         uid='document_form'
//         isOpen={true}
//         type='right'
//         form={this.refDocumentForm}
//       >
//         <FormDocument
//           modal={this.refDocumentModal}
//           id={this.state.showIdDocument}
//           onClose={() => this.setState({showIdDocument: 0} as FormCustomerState)}
//           descriptionSource="both"
//           description={{
//             defaultValues: {
//               creatingForModel: "Hubleto/App/Community/Customers/Models/CustomerDocument",
//               creatingForId: this.state.record.id,
//               origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
//             }
//           }}
//           isInlineEditing={this.state.showIdDocument < 0}
//           showInModalSimple={true}
//           onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
//             if (saveResponse.status == "success") {
//               this.loadRecord();
//               this.setState({ showIdDocument: 0 } as FormCustomerState)
//             }
//           }}
//           onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
//             if (saveResponse.status == "success") {
//               this.loadRecord();
//               this.setState({ showIdDocument: 0 } as FormCustomerState)
//             }
//           }}
//         />
//       </ModalForm>
//     );
//   }


//   renderTab(tabUid: string) {
//     const R = this.state.record;

//     switch (tabUid) {
//       case 'default':
//         // const customInputs = this.renderCustomInputs();

//         let mapAddress = '';
//         if (R.street_line_1 != '' && R.city != '' && R.COUNTRY && R.COUNTRY.name != '') {
//           mapAddress = R.street_line_1 + ', ' + R.postal_code + ' ' + R.city + ', ' + (R.region ? R.region + ', ' : '') + R.COUNTRY.name;
//         }

//         //@ts-ignore
//         const tmpCalendarSmall = <Calendar
//           onCreateCallback={() => this.loadRecord()}
//           initialView='dayGridMonth'
//           headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
//           eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?calendar=customers&idCustomer=' + R.id}
//           onDateClick={(date, time, info) => {
//             this.setState({
//               activityDate: date,
//               activityTime: time,
//               activityAllDay: false,
//               showIdActivity: -1,
//             } as FormDealState);
//           }}
//           onEventClick={(info) => {
//             this.setState({
//               showIdActivity: parseInt(info.event.id),
//             } as FormDealState);
//             info.jsEvent.preventDefault();
//           }}
//         ></Calendar>;

//         const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
//           <div className='mb-2'>
//             {tmpCalendarSmall}
//           </div>

//           <div className="hubleto component input"><div className="input-element w-full flex gap-2">
//             <input
//               className="w-full bg-blue-50 border border-blue-800 p-1 text-blue-800 placeholder-blue-300"
//               placeholder={this.translate('Type recent activity here')}
//               ref={this.refLogActivityInput}
//               onKeyUp={(event: any) => {
//                 if (event.keyCode == 13) {
//                   if (event.shiftKey) {
//                     this.scheduleActivity();
//                   } else {
//                     this.logCompletedActivity();
//                   }
//                 }
//               }}
//               onChange={(event: ChangeEvent<HTMLInputElement>) => {
//                 this.refLogActivityInput.current.value = event.target.value;
//               }}
//             />
//           </div></div>
//           <div className='mt-2'>
//             <button onClick={() => {this.logCompletedActivity()}} className="btn btn-blue-outline btn-small w-full">
//               <span className="icon"><i className="fas fa-check"></i></span>
//               <span className="text">{this.translate('Log completed activity')}</span>
//               <span className="shortcut">{this.translate('Enter')}</span>
//             </button>
//             <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
//               <span className="icon"><i className="fas fa-clock"></i></span>
//               <span className="text">{this.translate('Schedule activity')}</span>
//               <span className="shortcut">{this.translate('Shift+Enter')}</span>
//             </button>
//           </div>
//           {this.divider(this.translate('Most recent activities'))}
//           {R.ACTIVITIES ? <div className="list">{R.ACTIVITIES.reverse().slice(0, 7).map((item, index) => {
//             return <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
//               onClick={() => this.setState({showIdActivity: item.id} as FormDealState)}
//             >
//               <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
//               <span className="text">
//                 {item.subject}
//                 {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
//               </span>
//             </button>;
//           })}</div> : null}
//         </div>;

//         return <>
//           <div className='flex flex-col md:flex-row gap-2'>
//             <div className='flex-2 card'>
//               <div className="card-body flex flex-col md:flex-row gap-2">
//                 <div>
//                   {this.inputWrapper("name", {cssClass: 'text-2xl'})}
//                   {this.inputWrapper("identifier")}
//                   {this.inputWrapper("company_id")}
//                   {this.inputWrapper("street_line_1")}
//                   {this.inputWrapper("street_line_2")}
//                   {this.inputWrapper("postal_code")}
//                   {this.inputWrapper("city")}
//                   {this.inputWrapper("region")}
//                   {this.inputWrapper("id_country")}
//                   <div className="flex justify-between">
//                     {mapAddress == '' ? null :
//                       <div>
//                         <a
//                           href={"https://maps.google.com/?q=" + encodeURIComponent(mapAddress)}
//                           target="_blank"
//                           className="btn btn-transparent"
//                         >
//                           <span className="icon"><i className="fas fa-map"></i></span>
//                           <span className="text">{this.translate("Show on map")}</span>
//                         </a>
//                       </div>
//                     }
//                   </div>
//                   {this.inputWrapper('shared_folder')}
//                 </div>
//                 <div>
//                   {this.inputWrapper("id_owner")}
//                   {this.inputWrapper("id_manager")}
//                   {this.inputWrapper('note', {cssClass: 'bg-yellow-50 dark:bg-slate-600'})}
//                   {this.inputWrapper("tax_id")}
//                   {this.inputWrapper("vat_id")}
//                   {this.inputWrapper("date_created")}
//                   {this.inputWrapper("is_active")}
//                   <FormInput title={this.translate('Tags')}>
//                     <InputTags2
//                       {...this.getInputProps('tags')}
//                       value={this.state.record.TAGS}
//                       model="Hubleto/App/Community/Customers/Models/Tag"
//                       targetColumn="id_customer"
//                       sourceColumn="id_tag"
//                       colorColumn="_LOOKUP_COLOR"
//                       onChange={(input: any, value: any) => {
//                         R.TAGS = value;
//                         this.setState({record: R});
//                       }}
//                       onNewTag={(title: string) => {
//                         return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
//                       }}
//                     />
//                   </FormInput>
//                 </div>
//               </div>
//             </div>
//             {this.state.id > 0 ? <div>
//               <TableContacts
//                 uid={this.props.uid + "_table_contacts"}
//                 parentForm={this}
//                 showAsCards={true}
//                 idCustomer={R.id}
//                 customEndpointParams={{idCustomer: R.id}}
//               ></TableContacts>
//             </div> : null}
//           </div>
//           {/* {customInputs.length > 0 ?
//             <div className="card mt-2"><div className="card-header">{this.translate('Custom data')}</div><div className="card-body">
//               {customInputs}
//             </div></div>
//           : <></>} */}
//         </>

//       break;

//       case 'calendar':
//         //@ts-ignore
//         const tmpCalendarLarge = <Calendar
//           onCreateCallback={() => this.loadRecord()}
//           initialView='timeGridWeek'
//           views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
//           eventsEndpoint={globalThis.hubleto.config.projectUrl + '/calendar/api/get-calendar-events?calendar=customers&idCustomer=' + R.id}
//           onDateClick={(date, time, info) => {
//             this.setState({
//               activityDate: date,
//               activityTime: time,
//               showIdActivity: -1,
//             } as FormCustomerState);
//           }}
//           onEventClick={(info) => {
//             this.setState({
//               showIdActivity: parseInt(info.event.id),
//             } as FormCustomerState);
//             info.jsEvent.preventDefault();
//           }}
//         ></Calendar>;
//         return <>
//           {tmpCalendarLarge}
//           {this.state.showIdActivity == 0 ? null : this.renderActivityForm(R)}
//         </>;
//       break;

//       case 'documents':
//         return <>
//           <TableDocuments
//             key={this.state.tablesKey + "_table_customer_document"}
//             uid={this.props.uid + "_table_customer_documents"}
//             junctionModel='Hubleto\App\Community\Customers\Models\CustomerDocument'
//             junctionSourceColumn='id_customer'
//             junctionDestinationColumn='id_document'
//             junctionSourceRecordId={R.id}
//             readonly={!this.state.isInlineEditing}
//           />
//           {this.state.showIdDocument != 0 ? this.renderDocumentForm() : null}
//         </>
//       break;

//       default:
//         return super.renderTab(tabUid);
//       break;
//     }
//   }

// }
