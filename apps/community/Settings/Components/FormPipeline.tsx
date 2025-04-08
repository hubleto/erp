import React, { Component } from "react";
import { deepObjectMerge, getUrlParam } from "adios/Helper";
import HubletoForm, { HubletoFormProps, HubletoFormState, } from "../../../../src/core/Components/HubletoForm";
import TablePipelineSteps from "./TablePipelineSteps";

interface FormPipelineProps extends HubletoFormProps {}

interface FormPipelineState extends HubletoFormState {}

export default class FormPipeline<P, S> extends HubletoForm<FormPipelineProps, FormPipelineState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "HubletoApp/Community/Settings/Models/Pipeline",
  };

  props: FormPipelineProps;
  state: FormPipelineState;

  translationContext: string = "HubletoApp\\Community\\Settings\\Loader::Components\\FormPipeline";

  constructor(props: FormPipelineProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormPipelineProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam("recordId") == -1) {
      return (
        <>
          <h2>{"New Pipeline"}</h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ? this.state.record.name
              : "[Undefined Name]"}
          </h2>
        </>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div
          className="grid grid-cols-2 gap-1"
          style={{
            gridTemplateAreas: `
            'info info'
            'steps steps'
          `,
          }}
        >
          <div className="card mt-4" style={{ gridArea: "info" }}>
            <div className="card-header">Pipeline Information</div>
            <div className="card-body flex flex-row justify-around">
              {this.inputWrapper("name")}
              {this.inputWrapper("description")}
            </div>
          </div>

          <div className="card mt-4" style={{ gridArea: "steps" }}>
            <div className="card-header">Pipeline Steps</div>
            <div className="card-body">
              <TablePipelineSteps
                uid={this.props.uid + "_table_pipeline_steps_input"}
                context="Hello World"
                descriptionSource="props"
                data={{ data: R.PIPELINE_STEPS }}
                isUsedAsInput={true}
                isInlineEditing={this.state.isInlineEditing}
                onRowClick={() => this.setState({isInlineEditing: true})}
                onChange={(table: TablePipelineSteps) => {
                  this.updateRecord({ PIPELINE_STEPS: table.state.data?.data });
                }}
                description={{
                  ui: {
                    showFooter: false,
                    showHeader: false,
                  },
                  permissions: {
                    canCreate: true,
                    canDelete: true,
                    canRead: true,
                    canUpdate: true,
                  },
                  columns: {
                    name: { type: "varchar", title: "Name" },
                    order: { type: "int", title: "Order" },
                    color: { type: "color", title: "Color" },
                  },
                  inputs: {
                    name: { type: "varchar", title: "Name" },
                    order: { type: "int", title: "Order" },
                    color: { type: "color", title: "Color" },
                  },
                }}
              ></TablePipelineSteps>
              {this.state.isInlineEditing ? (
                <a
                  role="button"
                  onClick={() => {
                    if (!R.PIPELINE_STEPS) R.PIPELINE_STEPS = [];
                    R.PIPELINE_STEPS.push({
                      id_pipeline: { _useMasterRecordId_: true },
                    });
                    this.setState({ record: R });
                  }}
                >
                  + Add Pipeline Step
                </a>
              ) : null}
            </div>
          </div>
        </div>
      </>
    );
  }
}
