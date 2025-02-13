import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "adios/Helper";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import InputTags2 from "adios/Inputs/Tags2";
import FormInput from "adios/FormInput";
import TablePersons from "../../Contacts/Components/TablePersons";
import { TabPanel, TabView } from "primereact/tabview";
import FormActivity, {FormActivityProps, FormActivityState} from "./FormActivity";
import TableLeads from "../../Leads/Components/TableLeads";
import FormLead, {FormLeadProps, FormLeadState} from "../../Leads/Components/FormLead";
import ModalSimple from "adios/ModalSimple";
import TableDeals from "../../Deals/Components/TableDeals";
import FormDeal, {FormDealProps, FormDealState} from "../../Deals/Components/FormDeal";
import TableCustomerDocuments from "./TableCustomerDocuments";
import FormDocument, {FormDocumentProps, FormDocumentState} from "../../Documents/Components/FormDocument";
import FormPerson, {FormPersonProps, FormPersonState} from "../../Contacts/Components/FormPerson";
import Calendar from '../../Calendar/Components/Calendar'
import Hyperlink from "adios/Inputs/Hyperlink";

interface FormCustomerProps extends FormProps {
  highlightIdBussinessAccounts: number,
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  newEntryId?: number,
}

interface FormCustomerState extends FormState {
  //highlightIdBussinessAccounts: number,
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  createNewDocument: boolean,
  createNewPerson: boolean,
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityCalendarTimeClicked: string,
  activityCalendarDateClicked: string,
  //isInlineEditingBillingAccounts: boolean
}

export default class FormCustomer<P, S> extends Form<
  FormCustomerProps,
  FormCustomerState
> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: "HubletoApp/Community/Customers/Models/Customer",
  };

  props: FormCustomerProps;
  state: FormCustomerState;

  translationContext: string = 'hubleto.app.customers.formCustomer';

  constructor(props: FormCustomerProps) {
    super(props);

    this.state = {
      ...this.getStateFromProps(props),
      //highlightIdBussinessAccounts: this.props.highlightIdBussinessAccounts ?? 0,
      highlightIdActivity: this.props.highlightIdActivity ?? 0,
      createNewLead: false,
      createNewDeal: false,
      createNewDocument: false,
      createNewPerson: false,
      showIdDocument: 0,
      newEntryId: this.props.newEntryId ?? -1,
      showIdActivity: 0,
      activityCalendarTimeClicked: '',
      activityCalendarDateClicked: '',
      //isInlineEditingBillingAccounts: false,
    }
  }

  getStateFromProps(props: FormCustomerProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    if (record.PERSONS)
      record.PERSONS.map((item: any, key: number) => {
        record.PERSONS[key].id_customer = { _useMasterRecordId_: true };
      });
    if (record.ACTIVITIES)
      record.ACTIVITIES.map((item: any, key: number) => {
        record.ACTIVITIES[key].id_customer = { _useMasterRecordId_: true };
      });
    /* if (record.BILLING_ACCOUNTS) {
      record.BILLING_ACCOUNTS.map((item: any, key: number) => {
        record.BILLING_ACCOUNTS[key].id_customer = { _useMasterRecordId_: true };
        if (record.BILLING_ACCOUNTS[key].SERVICES) {
          record.BILLING_ACCOUNTS[key].SERVICES.map((item2: any, key2: number) => {
            record.BILLING_ACCOUNTS[key].SERVICES[key2].id_billing_account  = { _useMasterRecordId_: true };
          })
        }
      });
    } */
    if (record.TAGS)
      record.TAGS.map((item: any, key: number) => {
        record.TAGS[key].id_customer = { _useMasterRecordId_: true };
      });

    return record;
  }

  onBeforeSaveRecord(record: any) {
    //Delete all spaces in identifiers
    if (record.tax_id) record.tax_id = record.tax_id.replace(/\s+/g, "");
    if (record.vat_id) record.vat_id = record.vat_id.replace(/\s+/g, "");
    if (record.customer_id) record.customer_id = record.customer_id.replace(/\s+/g, "");

    return record;
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam("recordId") == -1) {
      return <h2>New Customer</h2>;
    } else {
      return <h2>{this.state.record.name ? this.state.record.name : "[Undefined Name]"}</h2>;
    }
  }

  renderNewPersonForm(R: any) {
    return (
      <ModalSimple
        uid='person_form'
        isOpen={true}
        type='right wide'
      >
        <FormPerson
          id={-1}
          creatingNew={true}
          isInlineEditing={true}
          descriptionSource="both"
          description={{
            defaultValues: {
              id_customer: R.id
            }
          }}
          showInModal={true}
          showInModalSimple={true}
          onClose={() => { this.setState({ createNewPerson: false } as FormCustomerState); }}
          onSaveCallback={(form: FormPerson<FormPersonProps, FormPersonState>, saveResponse: any) => {
            if (saveResponse.status = "success") {
              this.setState({createNewPerson: false} as FormCustomerState)
              this.loadRecord()
            }
          }}
        >
        </FormPerson>
      </ModalSimple>
    )
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

    return (
      <>
        <TabView>
          <TabPanel header={globalThis.main.translate('Customer')}>
            <div
              className="grid grid-cols-2 gap-1"
              style={{
                gridTemplateAreas: `
                  'customer customer'
                  'notes notes'
                  'contacts contacts'
                  'activities activities'
                `,
              }}
            >
              <div className="card" style={{ gridArea: "customer" }}>
                <div className="card-body flex flex-row gap-2">
                  <div className="w-1/2">
                    {this.inputWrapper("name")}
                    {this.inputWrapper("street_line_1")}
                    {this.inputWrapper("street_line_2")}
                    {this.inputWrapper("city")}
                    {this.inputWrapper("region")}
                    {this.inputWrapper("id_country")}
                    {this.inputWrapper("postal_code")}
                  </div>
                  <div className='border-l border-gray-200'></div>
                  <div className="w-1/2">
                    {this.inputWrapper("vat_id")}
                    {this.inputWrapper("customer_id")}
                    {this.inputWrapper("tax_id")}
                    {showAdditional ? this.inputWrapper("date_created") : null}
                    {this.inputWrapper("is_active")}
                    <FormInput title="Tags">
                      <InputTags2
                        {...this.getInputProps()}
                        value={this.state.record.TAGS}
                        model="HubletoApp/Community/Settings/Models/Tag"
                        targetColumn="id_customer"
                        sourceColumn="id_tag"
                        colorColumn="color"
                        onChange={(value: any) => {
                          this.updateRecord({ TAGS: value });
                        }}
                      />
                    </FormInput>
                    {this.inputWrapper("id_user")}
                  </div>
                </div>
              </div>

              <div className="card card-body"  style={{ gridArea: "notes" }}>
                {this.inputWrapper("note")}
              </div>

              {showAdditional ?
              <div className="card" style={{ gridArea: "contacts" }}>
                <div className="card-header">{globalThis.main.translate('Contact Persons')}</div>
                <div className="card-body">
                  <TablePersons
                    uid={this.props.uid + "_table_persons"}
                    showHeader={false}
                    showFooter={false}
                    descriptionSource="both"
                    customEndpointParams={{idCustomer: R.id}}
                    data={{ data: R.PERSONS }}
                    parentForm={this}
                    description={{
                      ui: {
                        addButtonText: globalThis.main.translate('Add contact person'),
                      },
                      columns: {
                        first_name: { type: "varchar", title: globalThis.main.translate("First name") },
                        last_name: { type: "varchar", title: globalThis.main.translate("Last name") },
                        is_main: { type: "boolean", title: globalThis.main.translate("Main Contact") },
                      },
                      inputs: {
                        first_name: { type: "varchar", title: globalThis.main.translate("First name") },
                        last_name: { type: "varchar", title: globalThis.main.translate("Last name") },
                        is_main: { type: "boolean", title: globalThis.main.translate("Main Contact") },
                      },
                    }}
                    isUsedAsInput={true}
                    readonly={false}
                    onRowClick={(table: TablePersons, row: any) => {
                      table.openForm(row.id);
                    }}
                    onChange={(table: TablePersons) => {
                      this.updateRecord({ PERSONS: table.state.data?.data });
                    }}
                    onDeleteSelectionChange={(table: TablePersons) => {
                      this.updateRecord({ PERSONS: table.state.data?.data ?? [] });
                    }}
                  ></TablePersons>
                  {this.state.isInlineEditing ? (
                    <a
                      role="button"
                      onClick={() => {
                        if (!R.PERSONS) R.PERSONS = [];
                        this.setState({createNewPerson: true} as FormCustomerState);
                      }}>
                      + {globalThis.main.translate('Add contact person')}
                    </a>
                  ) : null}
                  {this.state.createNewPerson ?
                    this.renderNewPersonForm(R)
                  : null}
                </div>
              </div>
              : null}
            </div>
          </TabPanel>
          {showAdditional ? (
            <TabPanel header={globalThis.main.translate('Calendar')}>
              <Calendar
                onCreateCallback={() => this.loadRecord()}
                readonly={R.is_archived}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                eventsEndpoint={globalThis.main.config.rewriteBase + 'customers/get-calendar-events?idCustomer=' + R.id}
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
                }}
              ></Calendar>
              {this.state.showIdActivity == 0 ? <></> :
                <ModalSimple
                  uid='activity_form'
                  isOpen={true}
                  type='right'
                >
                  <FormActivity
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
                    onSaveCallback={(form: FormActivity<FormActivityProps, FormActivityState>, saveResponse: any) => {
                      if (saveResponse.status == "success") {
                        this.setState({ showIdActivity: 0 } as FormCustomerState);
                      }
                    }}
                  ></FormActivity>
                </ModalSimple>
              }
            </TabPanel>
          ) : null}
          {showAdditional ? (
            <TabPanel header={globalThis.main.translate('Leads')}>
              <TableLeads
                uid={this.props.uid + "_table_leads"}
                data={{ data: R.LEADS }}
                descriptionSource="both"
                customEndpointParams={{idCustomer: R.id}}
                description={{
                  columns: {
                    title: { type: "varchar", title: "Title" },
                    price: { type: "float", title: "Amount" },
                    id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                    date_expected_close: { type: "date", title: "Expected Close Date" },
                  },
                  inputs: {
                    title: { type: "varchar", title: "Title" },
                    price: { type: "float", title: "Amount" },
                    id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                    date_expected_close: { type: "date", title: "Expected Close Date" },
                  },
                }}
                isUsedAsInput={false}
                readonly={false}
                onRowClick={(table: TableLeads, row: any) => {
                  table.openForm(row.id);
                }}
                onDeleteSelectionChange={(table: TableLeads) => {
                  this.updateRecord({ LEADS: table.state.data?.data ?? [] });
                }}
              />
              {this.state.isInlineEditing ? (
                <a
                  role="button"
                  onClick={() => {this.setState({ createNewLead: true } as FormCustomerState);}}>
                  + Add Lead
                </a>
              ) : <></>}
              {this.state.createNewLead == true ? (
                <ModalSimple
                  uid='lead_form'
                  isOpen={true}
                  type='right'
                >
                  <FormLead
                    id={-1}
                    isInlineEditing={true}
                    descriptionSource="both"
                    description={{
                      defaultValues: {
                        id_customer: R.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewLead: false } as FormCustomerState); }}
                    onSaveCallback={(form: FormLead<FormLeadProps, FormLeadState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({createNewLead: false} as FormCustomerState)
                      }
                    }}
                  />
                </ModalSimple>
              ): null}
            </TabPanel>
          ) : null}
          {showAdditional ? (
            <TabPanel header={globalThis.main.translate('Deals')}>
              <TableDeals
                uid={this.props.uid + "_table_deals"}
                data={{ data: R.DEALS }}
                descriptionSource="both"
                customEndpointParams={{idCustomer: R.id}}
                description={{
                  columns: {
                    title: { type: "varchar", title: "Title" },
                    price: { type: "float", title: "Amount" },
                    id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                    date_expected_close: { type: "date", title: "Expected Close Date" },
                  },
                  inputs: {
                    title: { type: "varchar", title: "Title" },
                    price: { type: "float", title: "Amount" },
                    id_currency: { type: "lookup", title: "Currency", model: 'HubletoApp/Community/Settings/Models/Currency' },
                    date_expected_close: { type: "date", title: "Expected Close Date" },
                  },
                }}
                isUsedAsInput={false}
                //isInlineEditing={this.state.isInlineEditing}
                readonly={false}
                onRowClick={(table: TableDeals, row: any) => {
                  table.openForm(row.id);
                }}
                onDeleteSelectionChange={(table: TableDeals) => {
                  this.updateRecord({ DEALS: table.state.data?.data ?? [] });
                }}
              />
              {this.state.isInlineEditing ? (
                <a
                  role="button"
                  onClick={() => {this.setState({ createNewDeal: true } as FormCustomerState);}}>
                  + Add Deal
                </a>
              ) : <></>}
              {this.state.createNewDeal == true ? (
                <ModalSimple
                  uid='deal_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDeal
                    id={-1}
                    isInlineEditing={true}
                    descriptionSource="both"
                    description={{
                      defaultValues: {
                        id_customer: R.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDeal: false } as FormCustomerState); }}
                    onSaveCallback={(form: FormDeal<FormDealProps, FormDealState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({createNewDeal: false} as FormCustomerState)
                      }
                    }}
                  />
                </ModalSimple>
              ): null}
            </TabPanel>
          ) : null}
          {showAdditional ? (
            <TabPanel header={globalThis.main.translate('Documents')}>
              <div className="divider"><div><div><div></div></div><div><span>{globalThis.main.translate('Shared documents')}</span></div></div></div>
              {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
              <div className="divider"><div><div><div></div></div><div><span>{globalThis.main.translate('Local documents')}</span></div></div></div>
              <TableCustomerDocuments
                uid={this.props.uid + "_table_deals"}
                data={{ data: R.DOCUMENTS }}
                descriptionSource="both"
                customEndpointParams={{idCustomer: R.id}}
                description={{
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  columns: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", cellRenderer: ( table: TableCustomerDocuments, data: any, options: any): JSX.Element => {
                      return (
                        <FormInput>
                          <Hyperlink {...this.getInputProps()}
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
                isUsedAsInput={true}
                //isInlineEditing={this.state.isInlineEditing}
                readonly={!this.state.isInlineEditing}
                onRowClick={(table: TableCustomerDocuments, row: any) => {
                  this.setState({showIdDocument: row.id_document} as FormCustomerState);
                }}
              />
              {this.state.isInlineEditing ? (
                <a
                  role="button"
                  onClick={() => this.setState({createNewDocument: true} as FormCustomerState)}
                >
                  + Add Document
                </a>
              ) : <></>}
              {this.state.createNewDocument == true ?
                <ModalSimple
                  uid='document_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDocument
                    id={-1}
                    descriptionSource="both"
                    isInlineEditing={true}
                    creatingForModel="Customer"
                    creatingForId={this.state.id}
                    description={{
                      defaultValues: {
                        creatingForModel: "Customer",
                        creatingForId: this.state.record.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDocument: false } as FormCustomerState) }}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ createNewDocument: false } as FormCustomerState)
                      }
                    }}
                  ></FormDocument>
                </ModalSimple>
              : null}
              {this.state.showIdDocument > 0 ?
                <ModalSimple
                  uid='document_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDocument
                    id={this.state.showIdDocument}
                    onClose={() => this.setState({showIdDocument: 0} as FormCustomerState)}
                    creatingForModel="Customer"
                    showInModal={true}
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
                </ModalSimple>
              : null}
            </TabPanel>
          ) : null}
        </TabView>

          {/* <div>
            <div className="card">
              <div className="card-header">this.state.record</div>
              <div className="card-body">
                <pre
                  style={{
                    color: "blue",
                    width: "100%",
                    fontFamily: "Courier New",
                    fontSize: "10px",
                  }}
                >
                  {JSON.stringify(R.PERSONS, null, 2)}
                </pre>
              </div>
            </div>
          </div> */}
        {/* </div> */}
      </>
    );
  }
}
