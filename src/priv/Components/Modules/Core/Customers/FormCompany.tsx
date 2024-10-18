import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "adios/Helper";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import TablePersons from "./TablePersons";
import TableActivities from "./TableActivities";
import TableBillingAccountServices from "../Billing/TableBillingAccountServices";
import request from "adios/Request";
import { TabPanel, TabView } from "primereact/tabview";
import { InputTextarea } from "primereact/inputtextarea";
import FormActivity from "./FormActivity";

interface FormCompanyProps extends FormProps {
  highlightIdBussinessAccounts: number,
  highlightIdActivity: number,
}

interface FormCompanyState extends FormState {
  highlightIdBussinessAccounts: number,
  highlightIdActivity: number,
  isInlineEditingBillingAccounts: boolean
}

export default class FormCompany<P, S> extends Form<
  FormCompanyProps,
  FormCompanyState
> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
  };

  props: FormCompanyProps;
  state: FormCompanyState;

  constructor(props: FormCompanyProps) {
    super(props);

    this.state = {
      ...this.getStateFromProps(props),
      highlightIdBussinessAccounts: this.props.highlightIdBussinessAccounts ?? 0,
      highlightIdActivity: this.props.highlightIdActivity ?? 0,
      isInlineEditingBillingAccounts: false,
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
        record.ACTIVITIES[key].id_user = globalThis.app.idUser;
      });
    if (record.BILLING_ACCOUNTS) {
      record.BILLING_ACCOUNTS.map((item: any, key: number) => {
        record.BILLING_ACCOUNTS[key].id_company = { _useMasterRecordId_: true };
        if (record.BILLING_ACCOUNTS[key].SERVICES) {
          record.BILLING_ACCOUNTS[key].SERVICES.map((item2: any, key2: number) => {
            record.BILLING_ACCOUNTS[key].SERVICES[key2].id_billing_account  = { _useMasterRecordId_: true };
          })
        }
      });
    }
    if (record.TAGS)
      record.TAGS.map((item: any, key: number) => {
        record.TAGS[key].id_person = { _useMasterRecordId_: true };
      });

    return record;
  }

  onBeforeSaveRecord(record: any) {
    if (record.id == -1) {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      record.date_created = `${year}-${month}-${day}`;
    }
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

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <TabView>
          <TabPanel header="Basic Information">
            <div
              className="grid grid-cols-2 gap-1"
              style={{
                gridTemplateAreas: `
                  'company company'
                  'contacts contacts'
                  'activities activities'
                `,
              }}
            >
              <div className="card" style={{ gridArea: "company" }}>
                <div className="card-header">Company Information</div>
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
                    {this.inputWrapper("tax_id")}
                    {this.inputWrapper("company_id")}
                    {showAdditional ? this.inputWrapper("date_created") : null}
                    {showAdditional ? this.inputWrapper("is_active") : null}
                    <FormInput title="Categories">
                      <InputTags2
                        {...this.getDefaultInputProps()}
                        value={this.state.record.TAGS}
                        model="CeremonyCrmApp/Modules/Core/Settings/Models/Tag"
                        targetColumn="id_company"
                        sourceColumn="id_tag"
                        colorColumn="color"
                        onChange={(value: any) => {
                          this.updateRecord({ TAGS: value });
                        }}
                      />
                    </FormInput>
                  </div>
                </div>
              </div>

              <div className="card" style={{ gridArea: "contacts" }}>
                <div className="card-header">Representatives</div>
                <div className="card-body">
                  <TablePersons
                    uid={this.props.uid + "_table_persons"}
                    showHeader={false}
                    showFooter={false}
                    data={{ data: R.PERSONS }}
                    description={{
                      permissions: {
                        canCreate: true,
                        canUpdate: true,
                        canDelete: true,
                        canRead: true,
                      },
                      columns: {
                        first_name: { type: "varchar", title: "First name" },
                        last_name: { type: "varchar", title: "Last name" },
                        __more_details: { type: "none", title: "", cellRenderer: ( table: TablePersons, data: any, options: any): JSX.Element => {
                            if (data.id > 0) {
                              return (<>
                                  <button
                                    className="btn btn-transparent btn-small"
                                    onClick={(e) => {
                                      e.preventDefault();
                                      table.openForm(data.id);
                                      return false;
                                    }}
                                  >
                                    <span className="icon"><i className="fas fa-external-link-alt"></i></span>
                                  </button>
                                </>
                              );
                            }
                          },
                        },
                      },
                    }}
                    isUsedAsInput={true}
                    isInlineEditing={this.state.isInlineEditing}
                    readonly={!this.state.isInlineEditing}
                    onRowClick={(table: TablePersons, row: any) => {
                      this.setState({ isInlineEditing: !this.state.isInlineEditing, });
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
                        R.PERSONS.push({
                          id_company: { _useMasterRecordId_: true },
                          is_primary: false,
                          is_active: true,
                        });
                        this.setState({ record: R });
                      }}>
                      + Add contact
                    </a>
                  ) : null}
                </div>
              </div>


            </div>
          </TabPanel>
          {showAdditional ? (
            <TabPanel header="Activities">
              <div className=" list">
                {R.ACTIVITIES && R.ACTIVITIES.length > 0 ?
                  R.ACTIVITIES.map((activity, key) => {
                    console.log(activity);

                    return (
                      <div className="list-item">
                        <button
                          onClick={() => {
                            if (this.state.highlightIdActivity == activity.id) {
                              this.setState({highlightIdActivity: 0} as FormCompanyState)
                            } else this.setState({highlightIdActivity: activity.id} as FormCompanyState)
                          }}
                          className={"w-full btn-list-item text-left text-sm p-2 hover:bg-gray-50 " + (this.state.highlightIdBussinessAccounts == activity.id ? "font-bold bg-gray-50" : "font-medium")}
                        > Open {activity.subject} {activity.date_start}
                        </button>
                        {this.state.highlightIdActivity == activity.id ?
                          <div className="card card-body m-2">
                            <FormActivity
                              id={activity.id}
                              onDeleteCallback={ () => {
                                R.ACTIVITIES.splice(key, 1);
                                this.setState({record: R});
                              }}
                            />
                          </div>
                        : null}
                      </div>
                    )
                  })
                : <span className="text">No activities</span>}
              </div>
              {/* <div className="card h-" style={{ gridArea: "activities" }}>
                <div className="card-header">Company Activities</div>
                <div className="card-body">
                  <InputTable
                    uid={this.props.uid + "_table_activities_input"}
                    {...this.getDefaultInputProps()}
                    value={R.ACTIVITIES}
                    onChange={(value: any) => {
                      this.updateRecord({ ACTIVITIES: value });
                    }}
                  >
                    <TableActivities
                      uid={this.props.uid + "_table_activities"}
                      context="Hello World"
                      descriptionSource="props"
                      description={{
                        permissions: {
                          canDelete: true,
                          canCreate: true,
                          canRead: true,
                          canUpdate: true,
                        },
                        columns: {
                          id_activity_type: { type: "lookup", title: "Type", model: "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType"},
                          subject: { type: "varchar", title: "Subject" },
                          date_start: { type: "date", title: "Start Date" },
                          time_start: { type: "time", title: "Start Time" },
                          date_end: { type: "date", title: "Date End" },
                          time_end: { type: "time", title: "Time End" },
                          all_day: { type: "boolean", title: "All day" },
                          completed: { type: "boolean", title: "Completed" },
                        },
                      }}
                    ></TableActivities>
                  </InputTable>
                  {this.state.isInlineEditing ? (
                    <a
                      role="button"
                      onClick={() => {
                        if (!R.ACTIVITIES) R.ACTIVITIES = [];
                        R.ACTIVITIES.push({
                          id_company: { _useMasterRecordId_: true },
                          completed: false,
                          id_user: false,
                        });
                        this.setState({ record: R });
                      }}>
                      + Add activity
                    </a>
                  ) : null}
                </div>
              </div> */}
            </TabPanel>
          ) : null}
          <TabPanel header="Billing Accounts">
            <div className="list">

              {R.BILLING_ACCOUNTS && R.BILLING_ACCOUNTS.length > 0
                ? R.BILLING_ACCOUNTS.map((input, key) => {
                  var servicesString = "";

                  if (input?.SERVICES) {
                    input.SERVICES.map((item, index) => {
                      if (item.SERVICE?.name) {
                        if (index == input.SERVICES.length-1) servicesString += item.SERVICE.name;
                        else servicesString += item.SERVICE.name + ", ";
                      }
                    })
                  }

                  return (
                    <>
                      <div className="list-item">
                        <button
                          onClick={() => { this.setState({highlightIdBussinessAccounts: input.id} as FormCompanyState) }}
                          className={"w-full btn-list-item text-left text-sm p-2 hover:bg-gray-50 " + (this.state.highlightIdBussinessAccounts == input.id ? "font-bold bg-gray-50" : "font-medium")}
                        >
                          <div className="flex grow justify-between">
                            <div className="grow">
                              <span className="break-all">{input.description}<br></br></span>
                              <small className="text text-gray-400">
                                Connected services: {(servicesString != "") ? servicesString : "None"}
                              </small>
                            </div>

                            <div className="flex justify-between gap-2">
                              <span className="icon"
                                onClick={()=> {this.setState({isInlineEditing: true})}}
                              >
                                <i className="fas fa-pencil-alt self-center"></i>
                              </span>
                              <span className="icon"><i className="fas fa-chevron-down self-center"></i></span>
                            </div>
                          </div>

                        </button>
                        {this.state.highlightIdBussinessAccounts == input.id ?
                          <div className="card card-body m-2">
                            <FormInput>
                              <div className="grid grid-cols-2 gap-4">
                                <label className="input-label self-center">Billing Account Description</label>
                                <InputVarchar
                                  {...this.getDefaultInputProps()}
                                  value={input.description}
                                  /* isInlineEditing={this.state.isInlineEditingBillingAccounts} */
                                  placeholder={globalThis.app.translate(
                                    "Billing Account Description"
                                  )}
                                  onChange={(value: any) => {
                                    this.updateRecord({
                                      BILLING_ACCOUNTS: { [key]: {description: value} },
                                    });
                                  }}/>
                              </div>
                            </FormInput>
                          </div>
                        : null}

                        {this.state.highlightIdBussinessAccounts == input.id ?
                          <div className="card mx-2 mb-2">
                            <div className="card-header text-sm">Services connected to the Billing Account</div>
                            <div className="card-body">
                              <InputTable
                                uid={this.props.uid + "_table_services_input"}
                                {...this.getDefaultInputProps()}
                                value={R.BILLING_ACCOUNTS[key].SERVICES ?? null}
                                /* isInlineEditing={this.state.isInlineEditingBillingAccounts} */
                                onChange={(value: any) => {
                                  this.updateRecord({
                                    BILLING_ACCOUNTS: { [key]: {SERVICES: value}
                                    },
                                  });
                                }}
                              >
                                <TableBillingAccountServices
                                  uid={this.props.uid + "_table_services"}
                                  context="Hello World"
                                  descriptionSource="props"
                                  description={{
                                    permissions: {
                                      canDelete: true,
                                      canCreate: true,
                                      canRead: true,
                                      canUpdate: true,
                                    },
                                    columns: {
                                      id_service: {
                                        type: "lookup",
                                        title: "Service Name",
                                        model: "CeremonyCrmApp/Modules/Core/Services/Models/Service",
                                      },
                                    },
                                  }}
                                ></TableBillingAccountServices>
                              </InputTable>
                              {this.state.isInlineEditing ? (
                                <a
                                  role="button"
                                  onClick={() => {
                                    if (!R.BILLING_ACCOUNTS[key].SERVICES) R.BILLING_ACCOUNTS[key].SERVICES = [];
                                    R.BILLING_ACCOUNTS[key].SERVICES.push({
                                      id_billing_account: { _useMasterRecordId_: true },
                                    });
                                    this.setState({ record: R });
                                  }}>
                                  + Connect another service
                                </a>
                              ) : null}
                            </div>
                          </div>
                        : null}
                        {this.state.highlightIdBussinessAccounts == input.id && this.state.isInlineEditing ?
                          <div className="mx-2 mb-2 flex flex-row justify-end">
                            <button
                              className="btn btn-danger text-sm"
                              onClick={() => {
                                globalThis.app.showDialogDanger(
                                  <>This will delete the <b>{input.description}</b> billing account and the connections to the services. Do you want to continue?</>,
                                  {
                                    header: "Delete billing account",
                                    footer: <>
                                      <button
                                        className="btn btn-danger"
                                        onClick={() => {
                                          request.get(
                                            'api/record/delete',
                                            {
                                              model: 'CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount',
                                              id: input.id,
                                            },
                                            (data: any) => {
                                              if (data.status == true || data.id == 0) {
                                                R.BILLING_ACCOUNTS.splice(key, 1);
                                                this.setState({record: R});
                                                globalThis.app.lastShownDialogRef.current.hide();
                                              }
                                            }
                                          );
                                        }}
                                      >
                                        <span className="icon"><i className="fas fa-trash-alt"></i></span>
                                        <span className="text">Yes, delete billing account</span>
                                      </button>
                                      <button
                                        className="btn btn-transparent"
                                        onClick={() => {
                                          globalThis.app.lastShownDialogRef.current.hide();
                                        }}
                                      >
                                        <span className="icon"><i className="fas fa-times"></i></span>
                                        <span className="text">No, do not delete billing account</span>
                                      </button>
                                    </>
                                  }
                                );
                              }}
                            >
                              <span className="icon"><i className="fas fa-trash-alt"></i></span>
                              <span className="text">Delete Billing Account</span>
                            </button>
                          </div>
                        : null}
                      </div>
                    </>
                  )
                })
                : <span className="text-sm p-1">No Billing Accounts</span>
              }
            </div>

            {/*<div>
              {this.state.isInlineEditingBillingAccounts ?
              <button className="btn btn-light flex justify-center"
                onClick={() => {
                  request.get(
                    'api/v1/record/save',
                    {
                      model: 'CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount',
                      id: input.id,
                      record: { ...input, description: input.description },
                    },
                    (data: any) => {
                      let record = {...this.state.record};
                      R.BILLING_ACCOUNTS[key] = data.savedRecord;
                      this.setState({record: record});
                    }
                  );
                }}
              ><i className="fa fa-floppy-disk self-center p-1"></i> Save</button>
              :
              <button className="btn btn-light flex justify-center"
                onClick={() => {this.setState({isInlineEditingBillingAccounts: true} as FormCompanyState)}}
              ><i className="fa fa-pencil-alt self-center p-1"></i></button>
              }
            </div> */}

            {this.state.isInlineEditing ? (
              <a
                role="button"
                onClick={() => {
                  if (!R.BILLING_ACCOUNTS) R.BILLING_ACCOUNTS = [];
                  R.BILLING_ACCOUNTS.push({
                    id_company: { _useMasterRecordId_: true },
                    description: "New Billing Account",
                  });
                  this.setState({ record: R });
                }}>
                + Add Billing Account
              </a>
            ) : null}
          </TabPanel>
          <TabPanel header="Notes">
            {this.input("note")}
          </TabPanel>
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
                  {JSON.stringify(R.BILLING_ACCOUNTS, null, 2)}
                </pre>
              </div>
            </div>
          </div> */}
        {/* </div> */}
      </>
    );
  }
}
