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
import Calendar from '../../../Core/Calendar/Components/Calendar';
import TableDealDocuments from './TableDealDocuments';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../../Core/Documents/Components/FormDocument';
import FormActivity, { FormActivityProps, FormActivityState } from './FormActivity';
import ModalSimple from 'adios/ModalSimple';

export interface FormDealProps extends FormProps {
  newEntryId?: number,
}

export interface FormDealState extends FormState {
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityCalendarTimeClicked: string,
  activityCalendarDateClicked: string,
}

export default class FormDeal<P, S> extends Form<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmMod/Sales/Deals/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  translationContext: string = 'mod.core.sales.formDeal';

  constructor(props: FormDealProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdDocument: 0,
      showIdActivity: 0,
      activityCalendarTimeClicked: '',
      activityCalendarDateClicked: '',
    };
    this.onCreateActivityCallback = this.onCreateActivityCallback.bind(this);
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
      'sales/deals/change-pipeline-step',
      {
        idStep: idStep,
        idPipeline: R.id_pipeline,
        idDeal: R.id
      },
      (data: any) => {
        if (data.status == "success") {
          R.id_pipeline_step = data.returnStep.id;
          R.HISTORY = data.dealHistory.reverse();
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
      'sales/deals/change-pipeline',
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

  onCreateActivityCallback() {
    this.loadRecord();
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (R.LEAD) R.LEAD.checkOwnership = false;

    if (R.HISTORY && R.HISTORY.length > 0) {
      if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
        R.HISTORY = this.state.record.HISTORY.reverse();
    }

    if (R.id > 0 && globalThis.app.idUser != R.id_user && !this.state.recordChanged) {
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
          <TabPanel header="Deal">
            <div className='grid grid-cols-2 gap-1' style=
              {{gridTemplateAreas:`
                'info info'
                'status status'
                'services services'
                'history history'
              `}}>
                <div className='card mt-2' style={{gridArea: 'info'}}>
                  <div className='card-body flex flex-row gap-2'>
                    <div className='grow'>
                      {this.inputWrapper('title', {readonly: R.is_archived})}
                      <FormInput title={"Company"} required={true}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmMod/Core/Customers/Models/Company'
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
                      <FormInput title={"Contact Person"}>
                        <Lookup {...this.getDefaultInputProps()}
                          model='CeremonyCrmMod/Core/Customers/Models/Person'
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
                          model='CeremonyCrmMod/Core/Settings/Models/Label'
                          targetColumn='id_deal'
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
                  <>
                    <div className='card mt-2' style={{gridArea: 'status'}}>
                      <div className='card-header'>Deal Progress</div>
                      <div className='card-body'>
                        <FormInput title={"Pipeline"}>
                          <Lookup {...this.getDefaultInputProps()}
                            readonly={R.is_archived}
                            model='CeremonyCrmMod/Core/Settings/Models/Pipeline'
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
                            uid={this.props.uid + "_table_deal_services"}
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
                                model: "CeremonyCrmMod/Core/Services/Models/Service",
                                cellRenderer: ( table: TableDealServices, data: any, options: any): JSX.Element => {
                                  return (
                                    <FormInput>
                                      <Lookup {...this.getDefaultInputProps()}
                                        model='CeremonyCrmMod/Core/Services/Models/Service'
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
                  </>

                : null}
            </div>
          </TabPanel>
          {showAdditional ?
            <TabPanel header={globalThis.app.translate('Calendar')}>
              <Calendar
                onCreateCallback={() => this.loadRecord()}
                readonly={R.is_archived}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                eventsEndpoint={globalThis.app.config.rewriteBase + '/sales/deals/get-calendar-events?idDeal=' + R.id}
                onDateClick={(date, time, info) => {
                  this.setState({
                    activityCalendarDateClicked: date,
                    activityCalendarTimeClicked: time,
                    showIdActivity: -1,
                  } as FormDealState);
                }}
                onEventClick={(info) => {
                  this.setState({
                    showIdActivity: parseInt(info.event.id),
                  } as FormDealState);
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
                        id_deal: R.id,
                        date_start: this.state.activityCalendarDateClicked,
                        time_start: this.state.activityCalendarTimeClicked == "00:00:00" ? null : this.state.activityCalendarTimeClicked,
                        date_end: this.state.activityCalendarDateClicked,
                      }
                    }}
                    showInModal={true}
                    showInModalSimple={true}
                    onClose={() => { this.setState({ showIdActivity: 0 } as FormDealState) }}
                    onSaveCallback={(form: FormActivity<FormActivityProps, FormActivityState>, saveResponse: any) => {
                      if (saveResponse.status == "success") {
                        this.setState({ showIdActivity: 0 } as FormDealState);
                      }
                    }}
                  ></FormActivity>
                </ModalSimple>
              }
            </TabPanel>
          : null}
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
                    id_document: { type: "lookup", title: "Document", model: "CeremonyCrmMod/Core/Documents/Models/Document" },
                  }
                }}
                isUsedAsInput={true}
                //isInlineEditing={this.state.isInlineEditing}
                readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                onRowClick={(table: TableDealDocuments, row: any) => {
                  this.setState({showIdDocument: row.id_document} as FormDealState);
                }}
              />
              {this.state.isInlineEditing  && !R.is_archived ?
                <a
                  role="button"
                  onClick={() => this.setState({showIdDocument: -1} as FormDealState)}
                >
                  + Add Document
                </a>
              : null}
              {this.state.showIdDocument < 0 ?
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
                    onClose={() => { this.setState({ showIdDocument: 0 } as FormDealState) }}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormDealState)
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
                    onClose={() => this.setState({showIdDocument: 0} as FormDealState)}
                    creatingForModel="Deal"
                    showInModal={true}
                    showInModalSimple={true}
                    readonly={R.is_archived}
                    onSaveCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormDealState)
                      }
                    }}
                    onDeleteCallback={(form: FormDocument<FormDocumentProps, FormDocumentState>, saveResponse: any) => {
                      if (saveResponse.status = "success") {
                        this.loadRecord();
                        this.setState({ showIdDocument: 0 } as FormDealState)
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
          {showAdditional ?
            <TabPanel header={globalThis.app.translate('History')}>
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
            </TabPanel>
          : null}
        </TabView>
      </>
    );
  }
}