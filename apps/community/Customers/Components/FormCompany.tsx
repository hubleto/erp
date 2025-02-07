import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "adios/Helper";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import InputTags2 from "adios/Inputs/Tags2";
import FormInput from "adios/FormInput";
import TablePersons from "./TablePersons";
import { TabPanel, TabView } from "primereact/tabview";
import FormActivity, {FormActivityProps, FormActivityState} from "./FormActivity";
import TableLeads from "../../Leads/Components/TableLeads";
import FormLead, {FormLeadProps, FormLeadState} from "../../Leads/Components/FormLead";
import ModalSimple from "adios/ModalSimple";
import TableDeals from "../../Deals/Components/TableDeals";
import FormDeal, {FormDealProps, FormDealState} from "../../Deals/Components/FormDeal";
import TableCompanyDocuments from "./TableCompanyDocuments";
import FormDocument, {FormDocumentProps, FormDocumentState} from "../../Documents/Components/FormDocument";
import FormPerson, {FormPersonProps, FormPersonState} from "./FormPerson";
import Calendar from '../../Calendar/Components/Calendar'

interface FormCompanyProps extends FormProps {
  highlightIdBussinessAccounts: number,
  highlightIdActivity: number,
  createNewLead: boolean,
  createNewDeal: boolean,
  newEntryId?: number,
}

interface FormCompanyState extends FormState {
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

export default class FormCompany<P, S> extends Form<
  FormCompanyProps,
  FormCompanyState
> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: "HubletoApp/Community/Customers/Models/Company",
  };

  props: FormCompanyProps;
  state: FormCompanyState;

  translationContext: string = 'hubleto.app.customers.formCompany';

  constructor(props: FormCompanyProps) {
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

  getStateFromProps(props: FormCompanyProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    if (record.PERSONS)
      record.PERSONS.map((item: any, key: number) => {
        record.PERSONS[key].id_company = { _useMasterRecordId_: true };
      });
    if (record.ACTIVITIES)
      record.ACTIVITIES.map((item: any, key: number) => {
        record.ACTIVITIES[key].id_company = { _useMasterRecordId_: true };
      });
    /* if (record.BILLING_ACCOUNTS) {
      record.BILLING_ACCOUNTS.map((item: any, key: number) => {
        record.BILLING_ACCOUNTS[key].id_company = { _useMasterRecordId_: true };
        if (record.BILLING_ACCOUNTS[key].SERVICES) {
          record.BILLING_ACCOUNTS[key].SERVICES.map((item2: any, key2: number) => {
            record.BILLING_ACCOUNTS[key].SERVICES[key2].id_billing_account  = { _useMasterRecordId_: true };
          })
        }
      });
    } */
    if (record.TAGS)
      record.TAGS.map((item: any, key: number) => {
        record.TAGS[key].id_company = { _useMasterRecordId_: true };
      });

    return record;
  }

  onBeforeSaveRecord(record: any) {
    //Delete all spaces in identifiers
    if (record.tax_id) record.tax_id = record.tax_id.replace(/\s+/g, "");
    if (record.vat_id) record.vat_id = record.vat_id.replace(/\s+/g, "");
    if (record.company_id) record.company_id = record.company_id.replace(/\s+/g, "");

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
      return <h2>New Company</h2>;
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
              id_company: R.id
            }
          }}
          showInModal={true}
          showInModalSimple={true}
          onClose={() => { this.setState({ createNewPerson: false } as FormCompanyState); }}
          onSaveCallback={(form: FormPerson<FormPersonProps, FormPersonState>, saveResponse: any) => {
            if (saveResponse.status = "success") {
              this.setState({createNewPerson: false} as FormCompanyState)
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
          <TabPanel header={globalThis.main.translate('Company')}>
            <div
              className="grid grid-cols-2 gap-1"
              style={{
                gridTemplateAreas: `
                  'company company'
                  'notes notes'
                  'contacts contacts'
                  'activities activities'
                `,
              }}
            >
              <div className="card" style={{ gridArea: "company" }}>
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
                    {this.inputWrapper("company_id")}
                    {this.inputWrapper("tax_id")}
                    {showAdditional ? this.inputWrapper("date_created") : null}
                    {this.inputWrapper("is_active")}
                    <FormInput title="Tags">
                      <InputTags2
                        {...this.getInputProps()}
                        value={this.state.record.TAGS}
                        model="HubletoApp/Community/Settings/Models/Tag"
                        targetColumn="id_company"
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
                    customEndpointParams={{idCompany: R.id}}
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
                        this.setState({createNewPerson: true} as FormCompanyState);
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
                eventsEndpoint={globalThis.main.config.rewriteBase + 'customers/get-calendar-events?idCompany=' + R.id}
                onDateClick={(date, time, info) => {
                  this.setState({
                    activityCalendarDateClicked: date,
                    activityCalendarTimeClicked: time,
                    showIdActivity: -1,
                  } as FormCompanyState);
                }}
                onEventClick={(info) => {
                  this.setState({
                    showIdActivity: parseInt(info.event.id),
                  } as FormCompanyState);
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
                        id_company: R.id,
                        date_start: this.state.activityCalendarDateClicked,
                        time_start: this.state.activityCalendarTimeClicked == "00:00:00" ? null : this.state.activityCalendarTimeClicked,
                        date_end: this.state.activityCalendarDateClicked,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ showIdActivity: 0 } as FormCompanyState) }}
                    onSaveCallback={(form: FormActivity<FormActivityProps, FormActivityState>, saveResponse: any) => {
                      if (saveResponse.status == "success") {
                        this.setState({ showIdActivity: 0 } as FormCompanyState);
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
                customEndpointParams={{idCompany: R.id}}
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
                  onClick={() => {this.setState({ createNewLead: true } as FormCompanyState);}}>
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
                        id_company: R.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewLead: false } as FormCompanyState); }}
                    onSaveCallback={(form: FormLead<FormLeadProps, FormLeadState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({createNewLead: false} as FormCompanyState)
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
                customEndpointParams={{idCompany: R.id}}
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
                  onClick={() => {this.setState({ createNewDeal: true } as FormCompanyState);}}>
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
                        id_company: R.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDeal: false } as FormCompanyState); }}
                    onSaveCallback={(form: FormDeal<FormDealProps, FormDealState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({createNewDeal: false} as FormCompanyState)
                      }
                    }}
                  />
                </ModalSimple>
              ): null}
            </TabPanel>
          ) : null}
          {showAdditional ? (
            <TabPanel header={globalThis.main.translate('Documents')}>
              <TableCompanyDocuments
                uid={this.props.uid + "_table_deals"}
                data={{ data: R.DOCUMENTS }}
                descriptionSource="both"
                customEndpointParams={{idCompany: R.id}}
                description={{
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  columns: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                  },
                  inputs: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                  },
                }}
                isUsedAsInput={true}
                //isInlineEditing={this.state.isInlineEditing}
                readonly={!this.state.isInlineEditing}
                onRowClick={(table: TableCompanyDocuments, row: any) => {
                  this.setState({showIdDocument: row.id_document} as FormCompanyState);
                }}
              />
              {this.state.isInlineEditing ? (
                <a
                  role="button"
                  onClick={() => this.setState({createNewDocument: true} as FormCompanyState)}
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
                    creatingForModel="Company"
                    creatingForId={this.state.id}
                    description={{
                      defaultValues: {
                        creatingForModel: "Company",
                        creatingForId: this.state.record.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDocument: false } as FormCompanyState) }}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ createNewDocument: false } as FormCompanyState)
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
                    onClose={() => this.setState({showIdDocument: 0} as FormCompanyState)}
                    creatingForModel="Company"
                    showInModal={true}
                    showInModalSimple={true}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormCompanyState)
                      }
                    }}
                    onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormCompanyState)
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
