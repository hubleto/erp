import React, { Component, ChangeEvent } from "react";
import { deepObjectMerge, getUrlParam } from "adios/Helper";
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from "adios/Inputs/Tags2";
import FormInput from "adios/FormInput";
import TableContacts from "../../Contacts/Components/TableContacts";
import { TabPanel, TabView } from "primereact/tabview";
import CustomerFormActivity, {CustomerFormActivityProps, CustomerFormActivityState} from "./CustomerFormActivity";
import TableLeads from "../../Leads/Components/TableLeads";
import FormLead, {FormLeadProps, FormLeadState} from "../../Leads/Components/FormLead";
import ModalForm from "adios/ModalForm";
import TableDeals from "../../Deals/Components/TableDeals";
import FormDeal, {FormDealProps, FormDealState} from "../../Deals/Components/FormDeal";
import TableCustomerDocuments from "./TableCustomerDocuments";
import FormDocument, {FormDocumentProps, FormDocumentState} from "../../Documents/Components/FormDocument";
import FormContact, {FormContactProps, FormContactState} from "../../Contacts/Components/FormContact";
import Calendar from '../../Calendar/Components/Calendar'
import Hyperlink from "adios/Inputs/Hyperlink";
import request from "adios/Request";
import { FormProps, FormState } from "adios/Form";
import moment, { Moment } from "moment";

export interface FormCustomerProps extends HubletoFormProps {
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  newEntryId?: number,
  tableContactsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
  tableDocumentsDescription?: any,
}

export interface FormCustomerState extends HubletoFormState {
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  createNewContact: boolean,
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityCalendarTimeClicked: string,
  activityCalendarDateClicked: string,
  tableValuesDescription?: any,
  tablesKey: number,
}

export default class FormCustomer<P, S> extends HubletoForm<FormCustomerProps, FormCustomerState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "HubletoApp/Community/Customers/Models/Customer",
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
      activityCalendarTimeClicked: '',
      activityCalendarDateClicked: '',
      tablesKey: 0,
    }
  }

  componentDidUpdate(prevProps: FormProps, prevState: FormState): void {
    if (prevState.isInlineEditing != this.state.isInlineEditing) this.setState({tablesKey: Math.random()} as FormCustomerState)
  }

  getStateFromProps(props: FormCustomerProps) {
    return {
      ...super.getStateFromProps(props),
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
      <h2>{this.state.record.name ? this.state.record.name : ''}</h2>
      <small>{this.translate('Customer')}</small>
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
              date_start: this.state.activityCalendarDateClicked,
              time_start: this.state.activityCalendarTimeClicked == "00:00:00" ? null : this.state.activityCalendarTimeClicked,
              date_end: this.state.activityCalendarDateClicked,
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

  // renderNewLeadForm(R: any): JSX.Element {
  //   return (
  //     <ModalForm
  //       uid='lead_form'
  //       isOpen={true}
  //       type='right'
  //     >
  //       <FormLead
  //         id={-1}
  //         isInlineEditing={true}
  //         descriptionSource="both"
  //         description={{
  //           defaultValues: {
  //             id_customer: R.id,
  //           }
  //         }}
  //         showInModal={true}
  //         showInModalSimple={true}
  //         onClose={() => { this.setState({ createNewLead: false } as FormCustomerState); }}
  //         onSaveCallback={(form: FormLead<FormLeadProps, FormLeadState>, saveResponse: any) => {
  //           if (saveResponse.status = "success") {
  //             console.log("hihi");

  //             this.loadRecord();
  //             this.setState({createNewLead: false} as FormCustomerState)
  //           }
  //         }}
  //       />
  //     </ModalForm>
  //   )
  // }

  // renderNewDealForm(R: any): JSX.Element{
  // return (
  //   <ModalForm
  //     uid='deal_form'
  //     isOpen={true}
  //     type='right'
  //   >
  //     <FormDeal
  //       id={-1}
  //       isInlineEditing={true}
  //       descriptionSource="both"
  //       description={{
  //         defaultValues: {
  //           id_customer: R.id,
  //         }
  //       }}
  //       showInModal={true}
  //       showInModalSimple={true}
  //       onClose={() => { this.setState({ createNewDeal: false } as FormCustomerState); }}
  //       onSaveCallback={(form: FormDeal<FormDealProps, FormDealState>, saveResponse: any) => {
  //         if (saveResponse.status = "success") {
  //           this.loadRecord();
  //           this.setState({createNewDeal: false} as FormCustomerState)
  //         }
  //       }}
  //     />
  //   </ModalForm>
  // )
  // }

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

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (R.LEADS && R.LEADS.length > 0) {
      R.LEADS.map((lead, index) => {
        lead.checkOwnership = false;
        if (lead.DEAL) lead.DEAL.checkOwnership = false;
      })
    }
    if (R.DEALS && R.DEALS.length > 0) {
      R.DEALS.map((deal, index) => {
        deal.checkOwnership = false;
        if (deal.LEAD) deal.LEAD.checkOwnership = false;
      })
    }

    let mapAddress = '';
    if (R.street_line_1 != '' && R.city != '' && R.COUNTRY && R.COUNTRY.name != '') {
      mapAddress = R.street_line_1 + ', ' + R.postal_code + ' ' + R.city + ', ' + (R.region ? R.region + ', ' : '') + R.COUNTRY.name;
    }

    let extraButtons = globalThis.main.injectDynamicContent('HubletoApp/Community/Customers/FormCustomer:ExtraButtons', {formCustomer: this});

    const recentActivitiesAndCalendar = <div className='card card-body mt-2 shadow-blue-200'>
      <div className="adios component input"><div className="input-element w-full flex gap-2">
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
          <span className="text">{this.translate('Enter = Log completed activity')}</span>
        </button>
        <button onClick={() => {this.scheduleActivity()}} className="btn btn-small w-full btn-blue-outline">
          <span className="icon"><i className="fas fa-clock"></i></span>
          <span className="text">{this.translate('Shift+Enter = Schedule')}</span>
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

      <div className='mt-2'>
        <Calendar
          onCreateCallback={() => this.loadRecord()}
          readonly={R.is_archived}
          initialView='dayGridMonth'
          headerToolbar={{ start: 'title', center: '', end: 'prev,today,next' }}
          eventsEndpoint={globalThis.main.config.accountUrl + '/calendar/api/get-calendar-events?source=customers&idCustomer=' + R.id}
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
        ></Calendar>
      </div>
    </div>;

    return <>
      <TabView>
        <TabPanel header={this.translate('Customer')}>
          <div className="gap-1">
            <div className='flex gap-1 mt-2'>
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
                    {showAdditional ? this.inputWrapper("date_created") : null}
                    {this.inputWrapper("is_active")}
                    <FormInput title="Tags">
                      <InputTags2
                        {...this.getInputProps('tags')}
                        value={this.state.record.TAGS}
                        model="HubletoApp/Community/Customers/Models/Tag"
                        targetColumn="id_customer"
                        sourceColumn="id_tag"
                        colorColumn="color"
                        onChange={(value: any) => {
                          R.TAGS = value;
                          this.setState({record: R});
                        }}
                      />
                    </FormInput>
                    {this.inputWrapper("id_owner")}
                    {this.inputWrapper("id_responsible")}
                  </div>
                </div>
              </div>
              {this.state.id > 0 ? <>
                <div className='flex-1'>
                  {recentActivitiesAndCalendar}
                </div>
              </> : null}
            </div>
            {extraButtons ? <div className="mt-2 p-2 bg-blue-50 flex gap-2">{extraButtons}</div> : null}
            {showAdditional ?
              <div className="card mt-2">
                <div className="card-header">{this.translate('Contacts')}</div>
                <div className="card-body">
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
                    isUsedAsInput={true}
                    readonly={!this.state.isInlineEditing}
                    customEndpointParams={{idCustomer: R.id}}
                    // data={{ data: R.CONTACTS }}
                    descriptionSource="props"
                    description={{
                      ui: {
                        showFulltextSearch: false,
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
                </div>
              </div>
            : null}
          </div>
        </TabPanel>
        {showAdditional ? (
          <TabPanel header={this.translate('Calendar')}>
            <Calendar
              onCreateCallback={() => this.loadRecord()}
              readonly={R.is_archived}
              initialView='timeGridWeek'
              views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
              eventsEndpoint={globalThis.main.config.accountUrl + '/calendar/api/get-calendar-events?source=customers&?idCustomer=' + R.id}
              onDateClick={(date, time, info) => {
                this.setState({
                  activityCalendarDateClicked: date,
                  activityCalendarTimeClicked: time,
                  showIdActivity: -1,
                } as FormCustomerState);
              }}
              onEventClick={(info) => {
                this.setState({
                  showIdActivity: parseInt(info.event.id),
                } as FormCustomerState);
                info.jsEvent.preventDefault();
              }}
            ></Calendar>
          </TabPanel>
        ) : null}
        {/* {showAdditional ? (
          <TabPanel header={this.translate('Leads') + (R.LEADS ? ' (' + R.LEADS.length + ')' : '')}>
            <a
              className="btn btn-add-outline mb-2"
              onClick={() => {this.setState({ createNewLead: true } as FormCustomerState);}}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">Add lead</span>
            </a>
            <TableLeads
              uid={this.props.uid + "_table_leads"}
              data={{ data: R.LEADS }}
              descriptionSource="both"
              customEndpointParams={{idCustomer: R.id}}
              isUsedAsInput={false}
              readonly={!this.state.isInlineEditing}
              description={{
                permissions: this.props.tableLeadsDescription.permissions,
                columns: {
                  title: { type: "varchar", title: "Title" },
                  price: { type: "float", title: "Price" },
                  id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                  date_expected_close: { type: "date", title: "Expected Close Date" },
                },
                inputs: {
                  title: { type: "varchar", title: "Title" },
                  price: { type: "float", title: "Price" },
                  id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                  date_expected_close: { type: "date", title: "Expected Close Date" },
                },
              }}
              onRowClick={(table: TableLeads, row: any) => {
                var tableProps = this.props.tableLeadsDescription
                tableProps.idLead = row.id;
                table.openForm(row.id);
              }}
              onDeleteSelectionChange={(table: TableLeads) => {
                this.updateRecord({ LEADS: table.state.data?.data ?? [] });
              }}
            />
            {this.state.createNewLead == true ? (
              this.renderNewLeadForm(R)
            ): null}
          </TabPanel>
        ) : null} */}
        {/* {showAdditional ? (
          <TabPanel header={this.translate('Deals') + (R.DEALS ? ' (' + R.DEALS.length + ')' : '')}>
            <a
              className="btn btn-add-outline mb-2"
              onClick={() => {this.setState({ createNewDeal: true } as FormCustomerState);}}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">Add deal</span>
            </a>
            <TableDeals
              uid={this.props.uid + "_table_deals"}
              data={{ data: R.DEALS }}
              descriptionSource="props"
              customEndpointParams={{idCustomer: R.id}}
              isUsedAsInput={false}
              readonly={!this.state.isInlineEditing}
              description={{
                permissions: this.props.tableDealsDescription.permissions,
                columns: {
                  title: { type: "varchar", title: "Title" },
                  price: { type: "float", title: "Price" },
                  id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                  date_expected_close: { type: "date", title: "Expected Close Date" },
                },
                inputs: {
                  title: { type: "varchar", title: "Title" },
                  price: { type: "float", title: "Price" },
                  id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                  date_expected_close: { type: "date", title: "Expected Close Date" },
                },
              }}
              onRowClick={(table: TableDeals, row: any) => {
                var tableProps = this.props.tableDealsDescription
                tableProps.idLead = row.id;
                table.onAfterLoadTableDescription(tableProps)
                table.openForm(row.id);
              }}
              onDeleteSelectionChange={(table: TableDeals) => {
                this.updateRecord({ DEALS: table.state.data?.data ?? [] });
              }}
            />
            {this.state.createNewDeal == true ? (
              this.renderNewDealForm(R)
            ): null}
          </TabPanel>
        ) : null} */}
        {showAdditional ? (
          <TabPanel header={this.translate('Documents')}>
            <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
            <a
              className="btn btn-add-outline mb-2"
              onClick={() => this.setState({showIdDocument: -1} as FormCustomerState)}
            >
              <span className="icon"><i className="fas fa-add"></i></span>
              <span className="text">Add document</span>
            </a>
            <TableCustomerDocuments
              key={this.state.tablesKey + "_table_documents"}
              uid={this.props.uid + "_table_documents"}
              data={{ data: R.DOCUMENTS }}
              descriptionSource="props"
              customEndpointParams={{idCustomer: R.id}}
              isUsedAsInput={true}
              readonly={!this.state.isInlineEditing}
              description={{
                permissions: this.props.tableDocumentsDescription?.permissions,
                ui: {
                  showFooter: false,
                  showHeader: false,
                },
                columns: {
                  id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                  hyperlink: { type: "varchar", title: "Link", cellRenderer: ( table: TableCustomerDocuments, data: any, options: any): JSX.Element => {
                    return (
                      <FormInput>
                        <Hyperlink {...this.getInputProps('document_link')}
                          value={data.DOCUMENT.hyperlink}
                          readonly={true}
                        ></Hyperlink>
                      </FormInput>
                    )
                  },},
                },
                inputs: {
                  id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                  hyperlink: { type: "varchar", title: "Link", readonly: true},
                },
              }}
              onRowClick={(table: TableCustomerDocuments, row: any) => {
                this.setState({showIdDocument: row.id_document} as FormCustomerState);
              }}
              onDeleteSelectionChange={(table) => {
                this.updateRecord({ DOCUMENTS: table.state.data?.data ?? []});
                this.setState({tablesKey: Math.random()} as FormCustomerState)
              }}
            />
            {this.state.showIdDocument != 0 ?
              this.renderDocumentForm()
            : null}
          </TabPanel>
        ) : null}
      </TabView>
      {this.state.showIdActivity == 0 ? <></> :
        this.renderActivityForm(R)
      }
    </>;
  }
}
