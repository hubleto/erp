import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import TableDealServices from './TableDealServices';
import Lookup from 'adios/Inputs/Lookup';
import { TabPanel, TabView } from 'primereact/tabview';
import CalendarComponent from '../Core/Calendar/CalendarComponent';
import TableDealDocuments from './TableDealDocuments';
import FormDocument from '../Core/Documents/FormDocument';
import ModalSimple from 'adios/ModalSimple';

interface FormDealProps extends FormProps {
  newEntryId?: number,
}

interface FormDealState extends FormState {
  newEntryId?: number,
  createNewDocument: boolean,
  showDocument: number,
  historyReversed: boolean,
}

export default class FormDeal<P, S> extends Form<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Deals/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  constructor(props: FormDealProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      createNewDocument: false,
      showDocument: 0,
      historyReversed: false
    };
  }

  getStateFromProps(props: FormDealProps) {
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
            {'New Deal'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Deal Name]'}
          </h2>
        </>
      );
    }
  }

  changeDealStatus(idStep: number, R: any) {
    if (idStep == R.id_pipeline_step) return;
    request.get(
      'sales/change-pipeline-step',
      {
        idStep: idStep,
        idPipeline: R.id_pipeline,
        idDeal: R.id
      },
      (data: any) => {
        if (data.status == "success") {
          R.id_pipeline_step = data.returnStep.id;
          R.HISTORY = data.dealHistory;
          R.PIPELINE_STEP = data.returnStep;
          this.setState({record: R});
        }
      }
    );
  }

  getDealSumPrice(recordServices: any) {
    var dealSumPrice = 0;
    recordServices.map((service, index) => {
      if (service.unit_price && service.amount) {
        var sum = service.unit_price * service.amount
        if (service.discount) {
          sum = sum - (sum * (service.discount / 100))
        }
        if (service.tax) {
          sum = sum - (sum * (service.tax / 100))
        }
        dealSumPrice += sum;
      }
    });
    return Number(dealSumPrice.toFixed(2));
  }

  pipelineChange(newRecord) {
    request.get(
      'sales/change-pipeline',
      {
        idPipeline: newRecord.id_pipeline
      },
      (data: any) => {
        if (data.status == "success") {
          newRecord.PIPELINE = data.newPipeline;
          newRecord.PIPELINE_STEP.order = 0;
          this.setState({record: newRecord});
        }
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (R.LEAD) R.LEAD.checkOwnership = false;

    if (R.HISTORY && R.HISTORY.length > 0 && this.state.historyReversed == false) {
      R.HISTORY = this.state.record.HISTORY.reverse();
      this.setState({historyReversed: true} as FormDealState);
    }

    if (R.id > 0 && globalThis.app.user.id != R.id_user && !this.state.recordChanged) {
      return (
        <>
          <div className='w-full h-full flex flex-col justify-center'>
            <span className='text-center'>This deal belongs to a different user</span>
          </div>
        </>
      )
    }

    return (
      <>
        <TabView>
          <TabPanel header="Basic Information">
            <div className='grid grid-cols-2 gap-1' style=
              {{gridTemplateAreas:`
                'info info'
                'status status'
                'services services'
                'history history'
              `}}>
                <div className='card mt-2' style={{gridArea: 'info'}}>
                  <div className='card-header'>Deal Information</div>
                  <div className='card-body flex flex-row gap-2'>
                    <div className='grow'>
                      {this.inputWrapper('title', {readonly: R.is_archived})}
                      <FormInput title={"Company"} required={true}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmApp/Modules/Core/Customers/Models/Company'
                          endpoint={`customers/get-company`}
                          value={R.id_company}
                          readonly={R.is_archived}
                          onChange={(value: any) => {
                            this.updateRecord({ id_company: value, id_person: null });
                            if (R.id_company == 0) {
                              R.id_company = null;
                              this.setState({record: R});
                            }
                          }}
                        ></Lookup>
                      </FormInput>
                      <FormInput title={"Contact Person"} required={true}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmApp/Modules/Core/Customers/Models/Person'
                          customEndpointParams={{id_company: R.id_company}}
                          endpoint={`customers/get-company-contacts`}
                          value={R.id_person}
                          readonly={R.is_archived}
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
                          readonly: (R.SERVICES && R.SERVICES.length > 0) || R.is_archived ? true : false,
                        })}
                        {this.inputWrapper('id_currency', {readonly: R.is_archived})}
                      </div>
                      {this.inputWrapper('id_deal_status', {readonly: R.is_archived})}
                      {showAdditional && R.id_lead != null ?
                        <div className='mt-2'>
                          <a className='btn btn-primary self-center' href={`leads?recordId=${R.id_lead}`}>
                            <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                            <span className='text'>Go to origin Lead</span>
                          </a>
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
                          targetColumn='id_deal'
                          sourceColumn='id_label'
                          colorColumn='color'
                          onChange={(value: any) => {
                            this.updateRecord({LABELS: value});
                          }}
                        ></InputTags2>
                      </FormInput>
                      {showAdditional ? this.inputWrapper('date_created', {readonly: R.is_archived}) : null}
                      {showAdditional ? this.inputWrapper('is_archived') : null}
                    </div>
                  </div>
                </div>
                {showAdditional ?
                  <>
                    <div className='card mt-2' style={{gridArea: 'status'}}>
                      <div className='card-header'>Deal Progress</div>
                      <div className='card-body'>
                        <FormInput title={"Pipeline"}>
                          <Lookup {...this.getDefaultInputProps()}
                            readonly={R.is_archived}
                            model='CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline'
                            value={R.id_pipeline}
                            onChange={(value: any) => {
                              this.updateRecord({ id_pipeline: value });
                              this.pipelineChange(R);
                            }}
                          ></Lookup>
                        </FormInput>
                        <div className=' flex flex-row gap-2 justify-center'>
                          {R.PIPELINE != null
                            && R.PIPELINE.PIPELINE_STEPS
                            && R.PIPELINE.PIPELINE_STEPS.length > 0 ?
                            R.PIPELINE.PIPELINE_STEPS.map((s, i) => {
                              var statusColor: string = null;
                              {R.PIPELINE_STEP && s.order <= R.PIPELINE_STEP.order ? statusColor = "btn-primary" : statusColor = "btn-light"}
                              return (
                                <>
                                  <button
                                    style={{height: "50px"}}
                                    onClick={R.is_archived ? null : ()=>{this.changeDealStatus(s.id, R)}}
                                    className={`flex px-3 justify-center btn ${statusColor}`}
                                  >
                                    <span className='text text-center self-center'>{s.name}</span>
                                  </button>
                                  {i+1 == R.PIPELINE.PIPELINE_STEPS.length ? null
                                  : <span className='icon flex'><i className='fas fa-angles-right self-center'></i></span>
                                  }
                                </>
                              )
                            })
                          : null}

                        </div>
                      </div>
                    </div>
                    {showAdditional ?
                      <div className='card mt-2' style={{gridArea: 'services'}}>
                        <div className='card-header'>Services</div>
                        <div className='card-body flex flex-col gap-2'>
                          <TableDealServices
                            uid={this.props.uid + "_table_lead_services"}
                            data={{ data: R.SERVICES }}
                            dealTotal={R.SERVICES && R.SERVICES.length > 0 ? "Total: " + R.price + " " + R.CURRENCY.code : null}
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
                                cellRenderer: ( table: TableDealServices, data: any, options: any): JSX.Element => {
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
                                            this.updateRecord({ price: this.getDealSumPrice(R.SERVICES)});
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
                                __sum: { type: "none", title: "Sum", cellRenderer: ( table: TableDealServices, data: any, options: any): JSX.Element => {
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
                            onChange={(table: TableDealServices) => {
                              this.updateRecord({ SERVICES: table.state.data?.data });
                              R.price = this.getDealSumPrice(R.SERVICES);
                              this.setState({record: R});
                            }}
                            onDeleteSelectionChange={(table: TableDealServices) => {
                              this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
                            }}
                          ></TableDealServices>
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
                                this.setState({ newEntryId: this.state.newEntryId - 1 } as FormDealState);
                              }}>
                              + Add service
                            </a>
                          ) : null}
                      </div>
                    : null}
                    <div className='card mt-2' style={{gridArea: 'history'}}>
                      <div className='card-header'>Deal History</div>
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
                          <p className='text-gray-400'>Deal has no history</p>
                        }
                      </div>
                    </div>
                  </>

                : null}
            </div>
          </TabPanel>
          <TabPanel header="Activities">
              <CalendarComponent
                creatingForModel="Deal"
                creatingForId={R.id}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                url={`../../customers/activities/get?creatingForModel=Deal&creatingForId=${R.id}`}
              ></CalendarComponent>
          </TabPanel>
          {showAdditional ? (
            <TabPanel header="Documents">
              <TableDealDocuments
                uid={this.props.uid + "_table_deal_documents"}
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
                onRowClick={(table: TableDealDocuments, row: any) => {
                  this.setState({showDocument: row.id_document} as FormDealState);
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
                    creatingForModel="Deal"
                    creatingForId={this.state.id}
                    description={{
                      defaultValues: {
                        creatingForModel: "Deal",
                        creatingForId: this.state.record.id,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ createNewDocument: false } as FormDealState) }}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ createNewDocument: false } as FormDealState)
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
                    onClose={() => this.setState({showDocument: 0} as FormDealState)}
                    creatingForModel="Deal"
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