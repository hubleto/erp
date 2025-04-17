import React, { Component, createRef } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';
import TableDealProducts from './TableDealProducts';
import Lookup from 'adios/Inputs/Lookup';
import { TabPanel, TabView } from 'primereact/tabview';
import Calendar from '../../Calendar/Components/Calendar';
import TableDealDocuments from './TableDealDocuments';
import FormDocument, { FormDocumentProps, FormDocumentState } from '../../Documents/Components/FormDocument';
import FormActivity, { FormActivityProps, FormActivityState } from './FormActivity';
import ModalSimple from 'adios/ModalSimple';
import Hyperlink from 'adios/Inputs/Hyperlink';
import moment from 'moment';

export interface FormDealProps extends HubletoFormProps {
  newEntryId?: number,
}

export interface FormDealState extends HubletoFormState {
  newEntryId?: number,
  showIdDocument: number,
  showIdActivity: number,
  activityCalendarTimeClicked: string,
  activityCalendarDateClicked: string,
  tableDealProductsDescription: any,
  tableDealDocumentsDescription: any,

}

export default class FormDeal<P, S> extends HubletoForm<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Deals/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\FormDeal';

  constructor(props: FormDealProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: this.props.newEntryId ?? -1,
      showIdDocument: 0,
      showIdActivity: 0,
      activityCalendarTimeClicked: '',
      activityCalendarDateClicked: '',
      tableDealProductsDescription: null,
      tableDealDocumentsDescription: null,
    };
    this.onCreateActivityCallback = this.onCreateActivityCallback.bind(this);
  }

  getStateFromProps(props: FormDealProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Deals/Models/DealProduct',
        idDeal: this.state.id,
      },
      (description: any) => {
        this.setState({tableDealProductsDescription: description} as any);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Deals/Models/DealDocument',
        idDeal: this.state.id,
      },
      (description: any) => {
        this.setState({tableDealDocumentsDescription: description} as any);
      }
    );

    return description;
  }

  onAfterSaveRecord(saveResponse: any): void {
    let params = this.getEndpointParams() as any;
    let isArchived = saveResponse.savedRecord.is_archived;

    if (params.showArchive == false && isArchived == true) {
      this.props.onClose();
      this.props.parentTable.loadData();
    }
    else if (params.showArchive == true && isArchived == false) {
      this.props.onClose();
      this.props.parentTable.loadData();
    } else super.onAfterSaveRecord(saveResponse);
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{this.translate('New Deal')}</h2>;
    } else {
      return <h2>{this.state.record.title ? this.state.record.title : '[Undefined Deal Name]'}</h2>
    }
  }

  renderSubTitle(): JSX.Element {
    return <small>{this.translate('Lead')}</small>;
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.tax) sum = sum + (sum * (product.tax / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
  }

  pipelineChange(idPipeline: number) {
    request.get(
      'deals/change-pipeline',
      {
        idPipeline: idPipeline
      },
      (data: any) => {
        if (data.status == "success") {
          var R = this.state.record;
          if (data.newPipeline.PIPELINE_STEPS?.length > 0) {
            R.id_pipeline = data.newPipeline.id;
            R.id_pipeline_step = data.newPipeline.PIPELINE_STEPS[0].id;
            R.deal_result = data.newPipeline.PIPELINE_STEPS[0].set_result;
            R.PIPELINE = data.newPipeline;
            R.PIPELINE_STEP = data.newPipeline.PIPELINE_STEPS[0];

            console.log(R);


            this.setState({ record: R });
          } else {
            R.id_pipeline = data.newPipeline.id;
            R.id_pipeline_step = null;
            R.PIPELINE = data.newPipeline;
            R.PIPELINE_STEP = null;

            this.setState({ record: R });
          }
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

    const lookupElement = createRef();
    var lookupData;

    const getLookupData = () => {
      if (lookupElement.current) {
        lookupData = lookupElement.current.state.data;
      }
    }

    if (R.LEAD) R.LEAD.checkOwnership = false;

    if (R.HISTORY && R.HISTORY.length > 0) {
      if (R.HISTORY.length > 1 && (R.HISTORY[0].id < R.HISTORY[R.HISTORY.length-1].id))
        R.HISTORY = this.state.record.HISTORY.reverse();
    }

    if (R.id > 0 && globalThis.main.idUser != R.id_user && !this.state.recordChanged) {
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
            {R.is_archived == 1 ?
              <div className='alert-warning mt-2 mb-1'>
                <span className='icon mr-2'><i className='fas fa-triangle-exclamation'></i></span>
                <span className='text'>This deal is archived.</span>
              </div>
            : null}
            <div className='grid grid-cols-2 gap-1' style=
              {{gridTemplateAreas:`
                'info info'
                'notes notes'
                'status status'
                'products products'
                'history history'
              `}}>
                <div className='card mt-2' style={{gridArea: 'info'}}>
                  <div className='card-body flex flex-row gap-2'>
                    <div className='grow'>
                      {this.inputWrapper('identifier', {readonly: R.is_archived})}
                      {this.inputWrapper('title', {readonly: R.is_archived})}
                      <FormInput title={"Customer"} required={true}>
                        <Lookup {...this.getInputProps("id_customer")}
                          model='HubletoApp/Community/Customers/Models/Customer'
                          endpoint={`customers/get-customer`}
                          value={R.id_customer}
                          readonly={R.is_archived}
                          onChange={(value: any) => {
                            this.updateRecord({ id_customer: value, id_person: null });
                            if (R.id_customer == 0) {
                              R.id_customer = null;
                              this.setState({record: R});
                            }
                          }}
                        ></Lookup>
                      </FormInput>
                      <FormInput title={"Contact Person"}>
                        <Lookup {...this.getInputProps("id_person")}
                          model='HubletoApp/Community/Customers/Models/Person'
                          customEndpointParams={{id_customer: R.id_customer}}
                          endpoint={`contacts/get-customer-contacts`}
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
                          readonly: (R.PRODUCTS && R.PRODUCTS.length > 0) || R.is_archived ? true : false,
                        })}
                        {this.inputWrapper('id_currency')}
                      </div>
                      {showAdditional && R.id_lead != null ?
                        <div className='mt-2'>
                          <a className='btn btn-primary self-center' href={`${globalThis.app.config.url}/leads/${R.id_lead}`}>
                            <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
                            <span className='text'>{this.translate('Go to original lead')}</span>
                          </a>
                        </div>
                      : null}
                    </div>
                    <div className='border-l border-gray-200'></div>
                    <div className='grow'>
                      {this.inputWrapper('id_user', {readonly: R.is_archived})}
                      {this.inputWrapper('date_expected_close', {readonly: R.is_archived})}
                      {this.inputWrapper('source_channel', {readonly: R.is_archived})}
                      <FormInput title='Tags'>
                        <InputTags2 {...this.getInputProps('tags')}
                          value={this.state.record.TAGS}
                          readonly={R.is_archived}
                          model='HubletoApp/Community/Deals/Models/Tag'
                          targetColumn='id_deal'
                          sourceColumn='id_tag'
                          colorColumn='color'
                          onChange={(value: any) => {
                            R.TAGS = value;
                            this.setState({record: R});
                          }}
                        ></InputTags2>
                      </FormInput>
                    </div>
                    <div className='border-l border-gray-200'></div>
                    <div className='grow'>
                      {this.inputWrapper("deal_result", {uiStyle: 'buttons'})}
                      {this.inputWrapper('is_new_customer')}
                      {this.inputWrapper('business_type', {uiStyle: 'buttons'})}
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
                          <Lookup {...this.getInputProps("id_pipeline")}
                            readonly={R.is_archived}
                            model='HubletoApp/Community/Settings/Models/Pipeline'
                            value={R.id_pipeline}
                            onChange={(value: any) => {
                              this.pipelineChange(value);
                            }}
                          ></Lookup>
                        </FormInput>
                        <div className='flex flex-row gap-2 flex-wrap justify-center'>
                          {R.PIPELINE != null &&
                          R.PIPELINE.PIPELINE_STEPS &&
                          R.PIPELINE.PIPELINE_STEPS.length > 0 ?
                            R.PIPELINE.PIPELINE_STEPS.map((s, i) => {
                              var statusColor: string = null;
                              {R.PIPELINE_STEP && s.order <= R.PIPELINE_STEP.order ? statusColor = "btn-primary" : statusColor = "btn-light"}
                              return (
                                <>
                                  <button
                                    onClick={R.is_archived ? null : ()=>{
                                      if (this.state.isInlineEditing == false) this.setState({isInlineEditing: true});
                                      R.id_pipeline_step = s.id;
                                      R.deal_result = s.set_result;
                                      R.PIPELINE_STEP = s;
                                      this.setState({record: R});
                                    }}
                                    className={`flex px-3 h-[50px] justify-center btn ${statusColor}`}
                                  >
                                    <span className='text text-center self-center !h-auto'>{s.name}</span>
                                  </button>
                                  {i+1 == R.PIPELINE.PIPELINE_STEPS.length ? null
                                  : <span className='icon flex'><i className='fas fa-angles-right self-center'></i></span>
                                  }
                                </>
                              )
                            })
                          : <p className='w-full text-center'>No steps exist for this pipeline</p>}
                        </div>
                      </div>
                    </div>
                    <div className='card card-body' style={{gridArea: 'notes'}}>
                      {this.inputWrapper('note', {readonly: R.is_archived})}
                    </div>
                    {showAdditional ?
                      <div className='card mt-2' style={{gridArea: 'products'}}>
                        <div className='card-header'>Products</div>
                        <div className='card-body flex flex-col gap-2'>
                          <div className='w-full h-full overflow-x-auto'>
                          {!R.is_archived ?
                            <a
                              className="btn btn-add-outline mb-2"
                              onClick={() => {
                                if (!R.PRODUCTS) R.PRODUCTS = [];
                                R.PRODUCTS.push({
                                  id: this.state.newEntryId,
                                  id_deal: { _useMasterRecordId_: true },
                                  amount: 1
                                });
                                this.setState({ record: R, isInlineEditing: true, newEntryId: this.state.newEntryId - 1 } as FormDealState);
                              }}
                            >
                            <span className="icon"><i className="fas fa-add"></i></span>
                            <span className="text">Add product</span>
                            </a>
                          : <></>}
                            <TableDealProducts
                              uid={this.props.uid + "_table_deal_products"}
                              data={{ data: R.PRODUCTS }}
                              dealTotal={R.PRODUCTS && R.PRODUCTS.length > 0 ? "Total: " + R.price + " " + R.CURRENCY.code : null}
                              descriptionSource='props'
                              customEndpointParams={{idDeal: R.id}}
                              description={{
                                permissions: this.state.tableDealProductsDescription?.permissions,
                                ui: {
                                  showHeader: false,
                                  showFooter: true
                                },
                                columns: {
                                  id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                                    cellRenderer: ( table: TableDealProducts, data: any, options: any): JSX.Element => {
                                      return (
                                        <FormInput>
                                          <Lookup {...this.getInputProps('lookup-product-1')}
                                            ref={lookupElement}
                                            model='HubletoApp/Community/Products/Models/Product'
                                            cssClass='min-w-44'
                                            value={data.id_product}
                                            onChange={(value: any) => {
                                              getLookupData();

                                              if (lookupData[value]) {
                                                data.id_product = value;
                                                data.unit_price = lookupData[value].unit_price;
                                                data.tax = lookupData[value].tax;
                                                this.updateRecord({ PRODUCTS: table.state.data?.data });
                                                this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                                              }
                                            }}
                                          ></Lookup>
                                        </FormInput>
                                      )
                                    },
                                  },
                                  unit_price: { type: "float", title: "Unit Price", readonly: true },
                                  amount: { type: "int", title: "Amount" },
                                  discount: { type: "float", title: "Discount (%)" },
                                  tax: { type: "float", title: "Tax (%)" },
                                  __sum: { type: "none", title: "Sum after tax",
                                    cellRenderer: ( table: TableDealProducts, data: any, options: any): JSX.Element => {
                                      if (data.unit_price && data.amount) {
                                        let sum = data.unit_price * data.amount;
                                        if (data.discount) sum = sum - (sum * (data.discount / 100));
                                        if (data.tax) sum = sum + (sum * (data.tax / 100));
                                        sum = Number(sum.toFixed(2));
                                        return (<><span>{sum + " " + R.CURRENCY.code}</span></>);
                                      }
                                    },
                                  },
                                },
                                inputs: {
                                  id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                                    cellRenderer: ( table: TableDealProducts, data: any, options: any): JSX.Element => {
                                      return (
                                        <FormInput>
                                          <Lookup {...this.getInputProps('lookup-product-2')}
                                            ref={lookupElement}
                                            model='HubletoApp/Community/Products/Models/Product'
                                            cssClass='min-w-44'
                                            value={data.id_product}
                                            onChange={(value: any) => {
                                              getLookupData();

                                              if (lookupData[value]) {
                                                data.id_product = value;
                                                data.unit_price = lookupData[value].unit_price;
                                                data.tax = lookupData[value].tax;
                                                this.updateRecord({ PRODUCTS: table.state.data?.data });
                                                this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                                              }
                                            }}
                                          ></Lookup>
                                        </FormInput>
                                      )
                                    },
                                  },
                                  unit_price: { type: "float", title: "Unit Price", readonly: true },
                                  amount: { type: "int", title: "Amount" },
                                  discount: { type: "float", title: "Discount (%)" },
                                  tax: { type: "float", title: "Tax (%)" },
                                  __sum: { type: "none", title: "Sum after tax"},
                                },
                              }}
                              isUsedAsInput={true}
                              isInlineEditing={this.state.isInlineEditing}
                              readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                              onRowClick={() => this.setState({isInlineEditing: true})}
                              onChange={(table: TableDealProducts) => {
                                this.updateRecord({ PRODUCTS: table.state.data?.data });
                                R.price = this.getSumPrice(R.PRODUCTS);
                                this.setState({record: R});
                              }}
                              onDeleteSelectionChange={(table: TableDealProducts) => {
                                this.updateRecord({ PRODUCTS: table.state.data?.data ?? [], price: this.getSumPrice(R.PRODUCTS) });
                              }}
                            ></TableDealProducts>
                          </div>
                        </div>
                      </div>
                    : null}
                  </>

                : null}
            </div>
          </TabPanel>
          {showAdditional ?
            <TabPanel header={this.translate('Calendar')}>
              <Calendar
                onCreateCallback={() => this.loadRecord()}
                readonly={R.is_archived}
                views={"timeGridDay,timeGridWeek,dayGridMonth,listYear"}
                eventsEndpoint={globalThis.main.config.rewriteBase + 'deals/get-calendar-events?idDeal=' + R.id}
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
                  info.jsEvent.preventDefault();
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
                    idCustomer={R.id_customer}
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
            <TabPanel header={this.translate("Documents")}>
              <div className="divider"><div><div><div></div></div><div><span>{this.translate('Shared documents')}</span></div></div></div>
              {this.inputWrapper('shared_folder', {readonly: R.is_archived})}
              <div className="divider"><div><div><div></div></div><div><span>{this.translate('Local documents')}</span></div></div></div>
              {!R.is_archived ?
                <a
                  className="btn btn-add-outline mb-2"
                  onClick={() => this.setState({showIdDocument: -1} as FormDealState)}
                >
                  <span className="icon"><i className="fas fa-add"></i></span>
                  <span className="text">Add document</span>
                </a>
              : null}
              <TableDealDocuments
                uid={this.props.uid + "_table_deal_documents"}
                data={{ data: R.DOCUMENTS }}
                customEndpointParams={{idDeal: R.id}}
                descriptionSource="props"
                description={{
                  permissions: this.state.tableDealDocumentsDescription?.permissions,
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  columns: {
                    id_document: { type: "lookup", title: "Document", model: "HubletoApp/Community/Documents/Models/Document" },
                    hyperlink: { type: "varchar", title: "Link", cellRenderer: ( table: TableDealDocuments, data: any, options: any): JSX.Element => {
                      return (
                        <FormInput>
                          <Hyperlink {...this.getInputProps('document-link')}
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
                  }
                }}
                isUsedAsInput={true}
                readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
                onRowClick={(table: TableDealDocuments, row: any) => {
                  this.setState({showIdDocument: row.id_document} as FormDealState);
                }}
              />
              {this.state.showIdDocument != 0 ?
                <ModalSimple
                  uid='document_form'
                  isOpen={true}
                  type='right'
                >
                  <FormDocument
                    id={this.state.showIdDocument}
                    onClose={() => this.setState({showIdDocument: 0} as FormDealState)}
                    showInModal={true}
                    descriptionSource="both"
                    description={{
                      defaultValues: {
                        creatingForModel: "HubletoApp/Community/Deals/Models/DealDocument",
                        creatingForId: this.state.record.id,
                        origin_link: window.location.pathname + "?recordId=" + this.state.record.id,
                      }
                    }}
                    isInlineEditing={this.state.showIdDocument < 0 ? true : false}
                    showInModalSimple={true}
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
          {showAdditional ?
            <TabPanel header={this.translate('History')}>
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