import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import InputTags2 from "@hubleto/react-ui/core/Inputs/Tags2";
import FormInput from "@hubleto/react-ui/core/FormInput";
import TableContacts from "../../Contacts/Components/TableContacts";
import { TabPanel, TabView } from "primereact/tabview";
import CustomerFormActivity, {CustomerFormActivityProps, CustomerFormActivityState} from "./CustomerFormActivity";
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import { FormDealState } from "../../Deals/Components/FormDeal";
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import FormDocument, {FormDocumentProps, FormDocumentState} from "../../Documents/Components/FormDocument";
import FormContact, {FormContactProps, FormContactState} from "../../Contacts/Components/FormContact";
import Calendar from '../../Calendar/Components/Calendar'
import Hyperlink from "@hubleto/react-ui/core/Inputs/Hyperlink";
import request from "@hubleto/react-ui/core/Request";
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";
import moment from "moment";

export interface FormCustomerProps extends HubletoFormProps {
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  newEntryId?: number,
  tableContactsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
}

export interface FormCustomerState extends HubletoFormState {
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  createNewContact: boolean,
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  tableValuesDescription?: any,
  tablesKey: number,
}

export default class FormCustomer<P, S> extends HubletoForm<FormCustomerProps, FormCustomerState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "HubletoApp/Community/Customers/Models/Customer"
  };

  props: FormCustomerProps;
  state: FormCustomerState;

  refLogActivityInput: any;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\FormCustomer';

  constructor(props: FormCustomerProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
      highlightIdActivity: this.props.highlightIdActivity ?? 0,
      createNewLead: false,
      createNewDeal: false,
      createNewContact: false,
      showIdDocument: 0,
      newEntryId: this.props.newEntryId ?? -1,
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      tablesKey: 0,
    }
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormCustomerState)
  }

  getStateFromProps(props: FormCustomerProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Customer')}</b> },
        { uid: 'contacts', title: this.translate('Contacts') },
        { uid: 'documents', title: this.translate('Documents') },
        { uid: 'calendar', icon: 'fas fa-calendar', position: 'right' },
      ],
    };
  }

  onBeforeSaveRecord(record: any) {
    //Delete all spaces in identifiers
    if (record.tax_id) record.tax_id = record.tax_id.replace(/\s+/g, "");
    if (record.vat_id) record.vat_id = record.vat_id.replace(/\s+/g, "");
    if (record.customer_id) record.customer_id = record.customer_id.replace(/\s+/g, "");

    return record;
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Customer')}</small>
      <h2>{this.state.record.name ? this.state.record.name : ''}</h2>
    </>;
  }

  onAfterFormInitialized(): void {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Contacts/Models/Value',
        idContact: -1,
      },
      (description: any) => {
        this.setState({tableValuesDescription: description} as FormCustomerState);
      }
    );
  }

  renderNewContactForm(R: any): JSX.Element {
    return (
      <ModalForm
        uid='contact_form'
        isOpen={true}
        type='right wide'
      >
        <FormContact
          id={-1}
          creatingNew={true}
          isInlineEditing={true}
          descriptionSource="both"
          tableValuesDescription={this.state.tableValuesDescription}
          description={{
            defaultValues: {
              id_customer: R.id
            }
          }}
          showInModal={true}
          showInModalSimple={true}
          onClose={() => { this.setState({ createNewContact: false } as FormCustomerState); }}
          onSaveCallback={(form: FormContact<FormContactProps, FormContactState>, saveResponse: any) => {
            if (saveResponse.status = "success") {
              this.setState({createNewContact: false} as FormCustomerState)
              this.loadRecord()
            }
          }}
        >
        </FormContact>
      </ModalForm>
    )
  }

  renderActivityForm(R: any): JSX.Element {
    return (
      <ModalForm
        uid='activity_form'
        isOpen={true}
        type='right theme-secondary'
      >
        <CustomerFormActivity
          id={this.state.showIdActivity}
          isInlineEditing={true}
          description={{
            defaultValues: {
              id_customer: R.id,
              date_start: this.state.activityDate,
              time_start: this.state.activityTime == "00:00:00" ? null : this.state.activityTime,
              date_end: this.state.activityDate,
              subject: this.state.activitySubject,
              all_day: this.state.activityAllDay,
            }
          }}
          showInModal={true}
          showInModalSimple={true}
          onClose={() => { this.setState({ showIdActivity: 0 } as FormCustomerState) }}
          onSaveCallback={(form: CustomerFormActivity<CustomerFormActivityProps, CustomerFormActivityState>, saveResponse: any) => {
            if (saveResponse.status == "success") {
              this.setState({ showIdActivity: 0 } as FormCustomerState);
            }
          }}
        ></CustomerFormActivity>
      </ModalForm>
     )
  }

  renderDocumentForm(): JSX.Element{
    return (
      <ModalForm
        uid='document_form'
        isOpen={true}
        type='right'
      >
        <FormDocument
          id={this.state.showIdDocument}
          onClose={() => this.setState({showIdDocument: 0} as FormCustomerState)}
          showInModal={true}
          descriptionSource="both"
          description={{
            defaultValues: {
              creatingForModel: "HubletoApp/Community/Customers/Models/CustomerDocument",
              creatingForId: this.state.record.id,
              origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
            }
          }}
          isInlineEditing={this.state.showIdDocument < 0 ? true : false}
          showInModalSimple={true}
          onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
            if (saveResponse.status = "success") {
              this.loadRecord();
              this.setState({ showIdDocument: 0 } as FormCustomerState)
            }
          }}
          onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
            if (saveResponse.status = "success") {
              this.loadRecord();
              this.setState({ showIdDocument: 0 } as FormCustomerState)
            }
          }}
        />
      </ModalForm>
    );
  }

  logCompletedActivity() {
    request.get(
      'customers/api/log-activity',
      {
        idCustomer: this.state.record.id,
        activity: this.refLogActivityInput.current.value,
      },
      (result: any) => {
        this.loadRecord();
        this.refLogActivityInput.current.value = '';
      }
    );
  }

  scheduleActivity() {
    this.setState({
      showIdActivity: -1,
      activityDate: moment().add(1, 'week').format('YYYY-MM-DD'),
      activityTime: moment().add(1, 'week').format('H:00:00'),
      activitySubject: this.refLogActivityInput.current.value,
      activityAllDay: false,
    } as FormDealState);
  }


  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        const customInputs = this.renderCustomInputs();

        let mapAddress = '';
        if (R.street_line_1 != '' && R.city != '' && R.COUNTRY && R.COUNTRY.name != '') {
          mapAddress = R.street_line_1 + ', ' + R.postal_code + ' ' + R.city + ', ' + (R.region ? R.region + ', ' : '') + R.COUNTRY.name;
        }

        //@ts-ignore
        const tmpCalendarSmall = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=customers&idCustomer=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              activityAllDay: false,
              showIdActivity: -1,
            } as FormDealState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormDealState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;

        const recentActivitiesAndCalendar = <div className='card card-body shadow-blue-200'>
          <div className='mb-2'>
            {tmpCalendarSmall}
          </div>

          <div className="hubleto component input"><div className="input-element w-full flex gap-2">
            <input
              className="w-full bg-blue-50 border border-blue-800 p-1 text-blue-800 placeholder-blue-300"
              placeholder={this.translate('Type recent activity here')}
              ref={this.refLogActivityInput}
              onKeyUp={(event: any) => {
                if (event.keyCode == 13) {
                  if (event.shiftKey) {
                    this.scheduleActivity();
                  } else {
                    this.logCompletedActivity();
                  }
                }
              }}
              onChange={(event: ChangeEvent<HTMLInputElement>) => {
                this.refLogActivityInput.current.value = event.target.value;
              }}
            />
          </div></div>
          <div className='mt-2'>
            <button onClick={() => {this.logCompletedActivity()}} className="btn btn-blue-outline btn-small w-full">
              <span className="icon"><i className="fas fa-check"></i></span>
              <span className="text">{this.translate('Log completed activity')}</span>
              <span className="shortcut">{this.translate('Enter')}</span>
            </button>
            <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
              <span className="icon"><i className="fas fa-clock"></i></span>
              <span className="text">{this.translate('Schedule activity')}</span>
              <span className="shortcut">{this.translate('Shift+Enter')}</span>
            </button>
          </div>
          {this.divider(this.translate('Most recent activities'))}
          {R.ACTIVITIES ? <div className="list">{R.ACTIVITIES.reverse().slice(0, 7).map((item, index) => {
            return <>
              <button key={index} className={"btn btn-small btn-transparent btn-list-item " + (item.completed ? "bg-green-50" : "bg-red-50")}
                onClick={() => this.setState({showIdActivity: item.id} as FormDealState)}
              >
                <span className="icon">{item.date_start} {item.time_start}<br/>@{item['_LOOKUP[id_owner]']}</span>
                <span className="text">
                  {item.subject}
                  {item.completed ? null : <div className="text-red-800">{this.translate('Not completed yet')}</div>}
                </span>
              </button>
            </>
          })}</div> : null}
        </div>;

        return <>
          <div className='flex gap-2'>
            <div className='flex-2 card'>
              <div className="card-body flex flex-row gap-2">
                <div className="w-1/2">
                  {this.inputWrapper("name", {cssClass: 'text-2xl text-primary'})}
                  {this.inputWrapper("customer_id")}
                  {this.inputWrapper("street_line_1")}
                  {this.inputWrapper("street_line_2")}
                  {this.inputWrapper("city")}
                  {this.inputWrapper("region")}
                  {this.inputWrapper("id_country")}
                  <div className="flex justify-between">
                    {this.inputWrapper("postal_code")}
                    {mapAddress == '' ? null :
                      <div>
                        <a
                          href={"https://maps.google.com/?q=" + encodeURIComponent(mapAddress)}
                          target="_blank"
                          className="btn btn-transparent"
                        >
                          <span className="icon"><i className="fas fa-map"></i></span>
                          <span className="text">{this.translate("Show on map")}</span>
                        </a>
                      </div>
                    }
                  </div>
                  {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
                </div>
                <div className='border-l border-gray-200'></div>
                <div className="w-1/2">
                  {this.inputWrapper('note', {cssClass: 'bg-yellow-50', readonly: R.is_archived})}
                  {this.inputWrapper("vat_id")}
                  {this.inputWrapper("tax_id")}
                  {this.inputWrapper("date_created")}
                  {this.inputWrapper("is_active")}
                  <FormInput title="Tags">
                    <InputTags2
                      {...this.getInputProps('tags')}
                      value={this.state.record.TAGS}
                      model="HubletoApp/Community/Customers/Models/Tag"
                      targetColumn="id_customer"
                      sourceColumn="id_tag"
                      colorColumn="color"
                      onChange={(input: any, value: any) => {
                        R.TAGS = value;
                        this.setState({record: R});
                      }}
                    />
                  </FormInput>
                  {this.inputWrapper("id_owner")}
                  {this.inputWrapper("id_manager")}
                </div>
              </div>
            </div>
            {this.state.id > 0 ? <>
              <div className='flex-1'>
                {recentActivitiesAndCalendar}
              </div>
            </> : null}
          </div>
          {customInputs.length > 0 ?
            <div className="card mt-2"><div className="card-header">Custom data</div><div className="card-body">
              {customInputs}
            </div></div>
          : <></>}
        </>

      break;

      case 'contacts':
        return <>
          <a
            className="btn btn-add-outline mr-2 float-left"
            onClick={() => {
              if (!R.CONTACTS) R.CONTACTS = [];
              this.setState({createNewContact: true} as FormCustomerState);
            }}
          >
            <span className="icon"><i className="fas fa-add"></i></span>
            <span className="text">{this.translate('Add contact')}</span>
          </a>
          <TableContacts
            uid={this.props.uid + "_table_contacts"}
            parentForm={this}
            showAsCards={true}
            isUsedAsInput={true}
            readonly={!this.state.isInlineEditing}
            customEndpointParams={{idCustomer: R.id}}
            // data={{ data: R.CONTACTS }}
            descriptionSource="props"
            description={{
              ui: {
                showFulltextSearch: true,
              },
              permissions: this.props.tableContactsDescription?.permissions ?? {},
              columns: {
                first_name: { type: "varchar", title: this.translate("First name") },
                last_name: { type: "varchar", title: this.translate("Last name") },
                virt_email: { type: "varchar", title: this.translate("Email"), },
                virt_number: { type: "varchar", title: this.translate("Phone number") },
                is_primary: { type: "boolean", title: this.translate("Primary Contact") },
                tags: { type: "none", title: this.translate("Tags") },
              },
              inputs: {
                first_name: { type: "varchar", title: this.translate("First name") },
                last_name: { type: "varchar", title: this.translate("Last name") },
                is_primary: { type: "boolean", title: this.translate("Primary Contact") },
              },
            }}
            onRowClick={(table: TableContacts, row: any) => {
              var tableProps = this.props.tableContactsDescription
              tableProps.idContact = row.id;
              table.onAfterLoadTableDescription(tableProps)
              table.openForm(row.id);
            }}
            onChange={(table: TableContacts) => {
              this.updateRecord({ CONTACTS: table.state.data?.data });
            }}
            onDeleteSelectionChange={(table: TableContacts) => {
              this.updateRecord({ CONTACTS: table.state.data?.data ?? [] });
            }}
          ></TableContacts>

          {this.state.createNewContact ?
            this.renderNewContactForm(R)
          : null}
        </>;
      break;

      case 'calendar':
        //@ts-ignore
        const tmpCalendarLarge = <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='timeGridWeek'
          views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
          eventsEndpoint={globalThis.main.config.projectUrl + '/calendar/api/get-calendar-events?source=customers&idCustomer=' + R.id}
          onDateClick={(date, time, info) => {
            this.setState({
              activityDate: date,
              activityTime: time,
              showIdActivity: -1,
            } as FormCustomerState);
          }}
          onEventClick={(info) => {
            this.setState({
              showIdActivity: parseInt(info.event.id),
            } as FormCustomerState);
            info.jsEvent.preventDefault();
          }}
        ></Calendar>;
        return <>
          {tmpCalendarLarge}
          {this.state.showIdActivity == 0 ? null : this.renderActivityForm(R)}
        </>;
      break;

      case 'documents':
        return <>
          {/* <a
            className="btn btn-add-outline mb-2"
            onClick={() => this.setState({showIdDocument: -1} as FormCustomerState)}
          >
            <span className="icon"><i className="fas fa-add"></i></span>
            <span className="text">Add document</span>
          </a> */}
          <TableDocuments
            key={this.state.tablesKey + "_table_customer_document"}
            uid={this.props.uid + "_table_customer_documents"}
            junctionModel='HubletoApp\Community\Customers\Models\CustomerDocument'
            junctionSourceColumn='id_customer'
            junctionDestinationColumn='id_document'
            junctionSourceRecordId={R.id}
            readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
          />
          {this.state.showIdDocument != 0 ? this.renderDocumentForm() : null}
        </>
      break;

      default:
        super.renderTab(tab);
      break;
    }
  }

}
