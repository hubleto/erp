import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import InputVarchar from 'adios/Inputs/Varchar';
import TableLeadServices from './TableLeadServices';
import { TabPanel, TabView } from 'primereact/tabview';
import CalendarComponent from '../Core/Calendar/CalendarComponent';
import Lookup from 'adios/Inputs/Lookup';
import TableLeadDocuments from './TableLeadDocuments';
import ModalSimple from 'adios/ModalSimple';
import FormDocument from '../Core/Documents/FormDocument';

interface FormLeadProps extends FormProps {
  newEntryId?: number,
}

interface FormLeadState extends FormState {
  newEntryId?: number,
  createNewDocument: boolean,
  showDocument: number,
  historyReversed: boolean,
}

export default class FormLead<P, S> extends Form<FormLeadProps,FormLeadState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Leads/Models/Lead',
  };

  props: FormLeadProps;
  state: FormLeadState;

  constructor(props: FormLeadProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      createNewDocument: false,
      showDocument: 0,
      historyReversed: false,
    };
  }

  getStateFromProps(props: FormLeadProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  /* normalizeRecord(record) {

    return record;
  } */

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
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Lead'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Lead Name]'}
          </h2>
        </>
      );
    }
  }

  getLeadSumPrice(recordServices: any) {
    var sumLeadPrice = 0;
    recordServices.map((service, index) => {
      if (service.unit_price && service.amount) {
        var sum = service.unit_price * service.amount
        if (service.discount) {
          sum = sum - (sum * (service.discount / 100))
        }
        if (service.tax) {
          sum = sum - (sum * (service.tax / 100))
        }
        sumLeadPrice += sum;
      }
    });
    return Number(sumLeadPrice.toFixed(2));
  }

  convertLead(recordId: number) {
    request.get(
      'sales/convert-lead',
      {recordId: recordId},
      (data: any) => {
        if (data.status == "success") {
          location.assign(`../sales/deals?recordId=${data.idDeal}&recordTitle=${data.title}`)
        }
      }
    );
  }

  convertDealWarning(recordId: number) {
    globalThis.app.showDialogDanger(
      <>Are you sure you want to convert this Lead to a Deal?</>,
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to a Deal",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertLead(recordId)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to a Deal</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => {
              globalThis.app.lastShownDialogRef.current.hide();
            }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert to a Deal</span>
          </button>
        </>
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;
    if (R.HISTORY && R.HISTORY.length > 0 && this.state.historyReversed == false) {
      R.HISTORY = this.state.record.HISTORY.reverse();
      this.setState({historyReversed: true} as FormLeadState);
    }

    if (R.DEAL) R.DEAL.checkOwnership = false;

    if (R.id > 0 && globalThis.app.user.id != R.id_user && !this.state.recordChanged) {
      return (
        <>
          <div className='w-full h-full flex flex-col justify-center'>
            <span className='text-center'>This lead belongs to a different user</span>
          </div>
        </>
      )
    }

    return (
      <>
        <TabView>
          <TabPanel header="Basic Information">
            {R.DEAL && R.is_archived == 1 ?
              <div className='alert-warning mt-2 mb-1'>
                <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
                <span className='text'>This lead was converted to a deal and cannot be edited</span>
              </div>
            : null}
            <div className='grid grid-cols-2 gap-1' style=
              {{gridTemplateAreas:`
                'notification notification'
                'info info'
                'services services'
                'history history'
              `}}>
                <div className='card mt-2' style={{gridArea: 'info'}}>
                  <div className='card-header'>Lead Information</div>
                  <div className='card-body flex flex-row gap-2'>
                    <div className='grow'>
                      {this.inputWrapper('title', {readonly: R.is_archived})}
                      <FormInput title={"Company"}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmApp/Modules/Core/Customers/Models/Company'
                          endpoint={`customers/get-company`}
                          readonly={R.is_archived}
                          value={R.id_company}
                          onChange={(value: any) => {
                            this.updateRecord({ id_company: value, id_person: null });
                            if (R.id_company == 0) {
                              R.id_company = null;
                              this.setState({record: R});
                            }
                          }}
                        ></Lookup>
                      </FormInput>
                      <FormInput title={"Contact Person"}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmApp/Modules/Core/Customers/Models/Person'
                          customEndpointParams={{id_company: R.id_company}}
                          readonly={R.is_archived}
                          endpoint={`customers/get-company-contacts`}
                          value={R.id_person}
                          onChange={(value: any) => {
                            this.updateRecord({ id_person: value })
                            if (R.id_person == 0) {
                              R.id_person = null;
                              this.setState({record: R})
                            }
                          }}
                        ></Lookup>
                      </FormInput>
                      <div className='flex flex-row *:w-1/2'>
                        {this.inputWrapper('price', {
                          readonly: (R.SERVICES && R.SERVICES.length) > 0 || R.is_archived ? true : false,
                        })}
                        {this.inputWrapper('id_currency', {readonly: R.is_archived})}
                      </div>
                      {showAdditional ? this.inputWrapper('id_lead_status', {readonly: R.is_archived}) : null}
                      {showAdditional ?
                        <div className='w-full mt-2'>
                          {R.DEAL != null ?
                          <a className='btn btn-primary' href={`../sales/deals?recordId=${R.DEAL.id}&recordTitle=${R.DEAL.title}`}>
                            <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                            <span className='text'>Go to Deal</span>
                          </a>
                          :
                          <a className='btn btn-primary cursor-pointer' onClick={() => this.convertDealWarning(R.id)}>
                            <span className='icon'><i className='fas fa-rotate-right'></i></span>
                            <span className='text'>Convert to Deal</span>
                          </a>}
                        </div>
                      : null}
                    </div>
                    <div className='border-l border-gray-200'></div>
                    <div className='grow'>
                      {this.inputWrapper('id_user', {readonly: R.is_archived})}
                      {this.inputWrapper('date_expected_close', {readonly: R.is_archived})}
                      {this.inputWrapper('source_channel', {readonly: R.is_archived})}
                      <FormInput title='Labels'>
                        <InputTags2 {...this.getDefaultInputProps()}
                          value={this.state.record.LABELS}
                          readonly={R.is_archived}
                          model='CeremonyCrmApp/Modules/Core/Settings/Models/Label'
                          targetColumn='id_lead'
                          sourceColumn='id_label'
                          colorColumn='color'
                          onChange={(value: any) => {
                            this.updateRecord({LABELS: value});
                          }}
                        ></InputTags2>
                      </FormInput>
                      {showAdditional ? this.inputWrapper('date_created') : null}
                      {showAdditional ? this.inputWrapper('is_archived') : null}
                    </div>
                  </div>
                </div>
                {showAdditional ?
                  <div className='card mt-2' style={{gridArea: 'services'}}>
                    <div className='card-header'>Services</div>
                    <div className='card-body flex flex-col gap-2'>
                      <TableLeadServices
                        uid={this.props.uid + "_table_lead_services"}
                        data={{ data: R.SERVICES }}
                        leadTotal={R.SERVICES && R.SERVICES.length > 0 ? "Total: " + R.price + " " + R.CURRENCY.code : null}
                        descriptionSource='props'
                        description={{
                          ui: {
                            showHeader: false,
                            showFooter: true
                          },
                          permissions: {
                            canCreate: true,
                            canUpdate: true,
                            canDelete: true,
                            canRead: true,
                          },
                          columns: {
                            id_service: { type: "lookup", title: "Service",
                              model: "CeremonyCrmApp/Modules/Core/Services/Models/Service",
                              cellRenderer: ( table: TableLeadServices, data: any, options: any): JSX.Element => {
                                return (
                                  <FormInput>
                                    <Lookup {...this.getDefaultInputProps()}
                                      model='CeremonyCrmApp/Modules/Core/Services/Models/Service'
                                      cssClass='min-w-44'
                                      value={data.id_service}
                                      onChange={(value: any) => {
                                        fetch('../services/get-service-price?serviceId='+value)
                                        .then(response => {
                                          if (!response.ok) {
                                            throw new Error('Network response was not ok ' + response.statusText);
                                          }
                                          return response.json();
                                        }).then(returnData => {
                                          data.id_service = value;
                                          data.unit_price = returnData.unit_price;
                                          this.updateRecord({ SERVICES: table.state.data?.data });
                                          this.updateRecord({ price: this.getLeadSumPrice(R.SERVICES)});
                                          console.log(table.state.data);
                                        })
                                      }}
                                    ></Lookup>
                                  </FormInput>
                                )
                              },
                            },
                            unit_price: { type: "float", title: "Unit Price" },
                            amount: { type: "int", title: "Amount" },
                            discount: { type: "float", title: "Discount (%)" },
                            tax: { type: "float", title: "Tax (%)" },
                            __sum: { type: "none", title: "Sum", cellRenderer: ( table: TableLeadServices, data: any, options: any): JSX.Element => {
                              if (data.unit_price && data.amount) {
                                var sum = data.unit_price * data.amount
                                if (data.discount) {
                                  sum = sum - (sum * (data.discount / 100))
                                }
                                if (data.tax) {
                                  sum = sum - (sum * (data.tax / 100))
                                }
                                sum = Number(sum.toFixed(2));
                                return (<>
                                    <span>{sum} {R.CURRENCY.code}</span>
                                  </>
                                );
                              }
                            },
                          },
                          },
                        }}
                        isUsedAsInput={true}
                        isInlineEditing={this.state.isInlineEditing}
                        readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                        onRowClick={() => this.setState({isInlineEditing: true})}
                        onChange={(table: TableLeadServices) => {
                          this.updateRecord({ SERVICES: table.state.data?.data });
                          R.price = this.getLeadSumPrice(R.SERVICES);
                          this.setState({record: R});
                        }}
                        onDeleteSelectionChange={(table: TableLeadServices) => {
                          this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                        }}
                      ></TableLeadServices>
                    </div>
                      {this.state.isInlineEditing && !R.is_archived ? (
                        <a
                          role="button"
                          onClick={() => {
                            if (!R.SERVICES) R.SERVICES = [];
                            R.SERVICES.push({
                              id: this.state.newEntryId,
                              id_lead: { _useMasterRecordId_: true },
                            });
                            this.setState({ record: R });
                            this.setState({ newEntryId: this.state.newEntryId - 1 } as FormLeadState);
                          }}>
                          + Add service
                        </a>
                      ) : null}
                  </div>
                : null}
                {showAdditional ?
                  <div className='card mt-2' style={{gridArea: 'history'}}>
                    <div className='card-header'>Lead History</div>
                    <div className='card-body min-h-[100px] flex justify-center' style={{flexDirection: "column", gap: "4px"}}>
                      {R.HISTORY.length > 0 ?
                        R.HISTORY.map((history, key) => (
                          <div className='w-full flex flex-row justify-between'>
                            <div className='w-1/3'>
                                <p className='font-bold self-center text-sm text-left'>
                                  {history.description}
                                </p>
                              </div>
                            <div className='w-1/3' style={{alignContent: "center"}}>
                              <hr style={{width: "100%", alignSelf: "center"}}/>
                            </div>
                            <div className='w-1/3 justify-center'>
                              <p className='self-center text-sm text-center'>
                                {history.change_date}
                              </p>
                            </div>
                          </div>
                        ))
                        :
                        <p className='text-gray-400'>Lead has no history</p>
                      }
                    </div>
                  </div>
                : null}
            </div>
          </TabPanel>
          {showAdditional ?
            <TabPanel header="Activities">
              <CalendarComponent
                readonly={R.is_archived}
                creatingForModel="Lead"
                creatingForId={R.id}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                url={`${window.ConfigEnv.rewriteBase}/customers/activities/get?creatingForModel=Lead&creatingForId=${R.id}`}
              ></CalendarComponent>
            </TabPanel>
          : null}
          {showAdditional ? (
            <TabPanel header="Documents">
              <TableLeadDocuments
                uid={this.props.uid + "_table_lead_document"}
                data={{ data: R.DOCUMENTS }}
                descriptionSource="props"
                description={{
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  permissions: {
                    canCreate: true,
                    canDelete: true,
                    canRead: true,
                    canUpdate: true
                  },
                  columns: {
                    id_document: { type: "lookup", title: "Document", model: "CeremonyCrmApp/Modules/Core/Documents/Models/Document" },
                  }
                }}
                isUsedAsInput={true}
                //isInlineEditing={this.state.isInlineEditing}
                readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                onRowClick={(table: TableLeadDocuments, row: any) => {
                  this.setState({showDocument: row.id_document} as FormLeadState);
                }}
              />
              {this.state.isInlineEditing  && !R.is_archived ?
                <a
                  role="button"
                  onClick={() => this.setState({createNewDocument: true} as FormDealState)}
                >
                  + Add Document
                </a>
              : null}
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
                    creatingForModel="Lead"
                    creatingForId={this.state.id}
                    description={{
                      defaultValues: {
                        creatingForModel: "Lead",
                        creatingForId: this.state.record.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDocument: false } as FormLeadState) }}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ createNewDocument: false } as FormLeadState)
                      }
                    }}
                  ></FormDocument>
                </ModalSimple>
              : null}
              {this.state.showDocument > 0 ?
                <ModalSimple
                  uid='document_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDocument
                    id={this.state.showDocument}
                    onClose={() => this.setState({showDocument: 0} as FormLeadState)}
                    creatingForModel="Lead"
                    showInModal={true}
                    showInModalSimple={true}
                    readonly={R.is_archived}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                      }
                    }}
                  />
                </ModalSimple>
              : null}
            </TabPanel>
          ) : null}
          <TabPanel header="Notes">
            {this.inputWrapper('note', {readonly: R.is_archived})}
          </TabPanel>
        </TabView>
      </>
    );
  }
}