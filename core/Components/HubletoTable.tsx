import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import HubletoForm, { HubletoFormProps, HubletoFormState } from './HubletoForm';
import HubletoTableExportCsvForm from './HubletoTableExportCsvForm';
import HubletoTableImportCsvForm from './HubletoTableImportCsvForm';
import { getUrlParam } from 'adios/Helper';
import ModalForm from "adios/ModalForm";
import HubletoTableColumnsCustomize from './HubletoTableColumnsCustomize';

export interface HubletoTableProps extends TableProps {
}

export interface HubletoTableState extends TableState {
  sidebarFilterHidden: boolean,
  showExportCsvScreen: boolean,
  showImportCsvScreen: boolean,
  showColumnConfigScreen: boolean,
}

export default class HubletoTable<P, S> extends Table<HubletoTableProps, HubletoTableState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
  }

  props: HubletoTableProps;
  state: HubletoTableState;

  getStateFromProps(props: HubletoTableProps) {
    return {
      ...super.getStateFromProps(props),
      sidebarFilterHidden: false,
      showExportCsvScreen: false,
      showImportCsvScreen: false,
      showColumnConfigScreen: false,
    };
  }

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right'
      }
    } else return {...super.getFormModalProps()}
  }

  renderSidebarFilter(): JSX.Element {
    if (this.state?.description?.ui?.defaultFilters) {
      return <div className="border-r border-r-gray-100 pr-2 h-full">
        <button className="btn btn-transparent"
          onClick={() => this.setState({sidebarFilterHidden: !this.state.sidebarFilterHidden})}
        >
          <span className="icon"><i className={"fas fa-" + (this.state.sidebarFilterHidden ? "arrow-right" : "arrow-left")}></i></span>
          {this.state.sidebarFilterHidden ? null : <span className="text">Hide filter</span>}
        </button>
        {this.state.sidebarFilterHidden ? null :
          <div className="flex flex-col gap-2 text-nowrap mt-2">
            {Object.keys(this.state.description.ui.defaultFilters).map((filterName) => {
              const filter = this.state.description.ui.defaultFilters[filterName];
              const filterValue = this.state.defaultFilters[filterName] ?? null;

              return <div key={filterName}>
                <b>{filter.title}</b>
                <div className="list">
                  {Object.keys(filter.options).map((key: any) => {
                    return <button
                      key={key}
                      className={"btn btn-small btn-list-item " + (filterValue == key ? "btn-primary" : "btn-transparent")}
                      onClick={() => {
                        let defaultFilters = this.state.defaultFilters ?? {};

                        if (filter.type == 'multipleSelectButtons') {
                          if (filterValue) {
                            if (filterValue.includes(key)) {
                              defaultFilters[filterName] = [];
                              for (let i in filterValue) {
                                if (filterValue[i] != key) defaultFilters[filterName].push(filterValue[i]);
                              }
                            } else {
                              console.log('b');
                              defaultFilters[filterName] = filterValue;
                              defaultFilters[filterName].push(key);
                            }
                          } else {
                            defaultFilters[filterName] = [ key ];
                          }
                        } else {
                          defaultFilters[filterName] = key;
                        }

                        this.setState({defaultFilters: defaultFilters}, () => this.loadData());
                      }}
                    >
                      {filter.type == 'multipleSelectButtons' ?
                        <span className="icon"><input type="checkbox" checked={filterValue && filterValue.includes(key)}></input></span>
                      : null}
                      <span className="text">{filter.options[key]}</span>
                    </button>;
                  })}
                </div>
              </div>;
            })}
          </div>
        }
      </div>;
    } else {
      return <></>;
    }
  }

  renderForm(): JSX.Element {
    let formProps: HubletoFormProps = this.getFormProps();
    return <HubletoForm {...formProps}/>;
  }

  renderContent(): JSX.Element {
    return <>
      {super.renderContent()}
      {this.state.showExportCsvScreen ?
        <ModalForm
          uid={this.props.uid + '_export_csv_modal'}
          isOpen={true}
          type='centered large'
        >
          <HubletoTableExportCsvForm
            model={this.props.model}
            parentTable={this}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showExportCsvScreen: false}); }}
          ></HubletoTableExportCsvForm>
        </ModalForm>
      : null}
      {this.state.showImportCsvScreen ?
        <ModalForm
          uid={this.props.uid + '_import_csv_modal'}
          isOpen={true}
          type='centered large'
        >
          <HubletoTableImportCsvForm
            model={this.props.model}
            parentTable={this}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showImportCsvScreen: false}); }}
          ></HubletoTableImportCsvForm>
        </ModalForm>
      : null}
      {this.state.showColumnConfigScreen ?
        <ModalForm
          uid={this.props.uid + '_columns_config_modal'}
          isOpen={true}
          type='right'
          title='Customize Columns'
        >
          <HubletoTableColumnsCustomize
            parentTable={this}
            tableTag={this.props.tag}
            tableModel={this.model}
            onClose={() => this.setState({showColumnConfigScreen: false})}
          ></HubletoTableColumnsCustomize>
        </ModalForm>
      : null}
    </>;
  }
}